# Movie Booking API

A production-architecture REST API for a franchise-based movie ticket booking platform. Built with Laravel 11, the system handles multi-role access control, real-time seat availability, concurrent booking prevention, TMDB movie sync, and Razorpay payment processing.

**Repository:** Backend API only — frontend is maintained in a separate repository.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Database Schema](#database-schema)
- [API Reference](#api-reference)
- [Key Engineering Decisions](#key-engineering-decisions)
- [Installation](#installation)
- [Environment Variables](#environment-variables)
- [Running Tests](#running-tests)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 11, PHP ^8.2 |
| Database | MySQL 8 |
| Authentication | Laravel Sanctum |
| Authorization | Spatie Laravel Permission |
| Payment | Razorpay |
| External API | TMDB (The Movie Database) |
| Queue | Database-backed (Laravel Queue) |
| Testing | PHPUnit 11, Mockery |
| Containerisation | Docker, Docker Compose |

---

## Architecture

```
app/
├── DTOs/
│   └── MovieDTO.php             # Readonly class mapping TMDB response to internal structure
├── Exceptions/                  # Typed domain exceptions
│   ├── ApiAuthException.php
│   ├── ApiConnectionException.php
│   ├── ApiRateLimitException.php
│   └── Booking/SeatUnavailableException.php
├── Http/
│   ├── Controllers/             # Thin controllers — delegate to services
│   ├── Requests/                # Input validation via Form Request classes
│   └── Resources/               # API Resources for consistent response shaping
├── Jobs/
│   ├── FetchUpcomingMoviesJob.php     # Paginated TMDB sync, multi-language
│   └── ShowSeat/
│       ├── PopulateShowSeatsJob.php   # Bulk-inserts show_seat rows after show creation
│       └── UnlockShowSeatsJob.php     # Releases expired seat locks every 5 minutes
├── Models/                      # Eloquent models with typed status constants
├── Observers/
│   └── MovieShowObserver.php    # Dispatches PopulateShowSeatsJob on show creation
├── Policies/
│   ├── OwnerPolicy.php          # Base class — shared ownsTheater() ownership check
│   ├── TheaterPolicy.php
│   ├── ScreenPolicy.php
│   ├── SeatPolicy.php
│   ├── MovieShowPolicy.php
│   └── BookingPolicy.php
├── Repositories/
│   ├── Contracts/MovieRepositoryInterface.php
│   └── MovieRepository.php      # Stale check, upsert, search queries
├── Services/
│   ├── BookingService.php       # Seat locking, transaction, booking creation
│   ├── MovieService.php         # DB-first fetch with API fallback
│   ├── Payment/
│   │   ├── Contracts/PaymentGatewayInterface.php
│   │   ├── RazorpayService.php  # Implements PaymentGatewayInterface
│   │   └── PaymentService.php   # Order creation and webhook confirmation
│   └── ExternalApi/
│       ├── Contracts/MovieApiInterface.php
│       ├── Http/ApiClient.php   # Retry, backoff, rate-limit handling
│       └── TmdbApiService.php
└── Traits/
    └── ApiResponse.php          # Standardised JSON envelope across all controllers
```

---

## Database Schema

12 tables across the full booking and payment lifecycle:

| Table | Purpose |
|---|---|
| `users` | Authentication, Spatie role assignment |
| `movies` | TMDB-sourced data, synced periodically via background job |
| `theaters` | Physical locations with soft deletes |
| `theater_owners` | Pivot — assigns owners to theaters, stores `assigned_by` for audit |
| `screens` | Screens within a theater (type, capacity) with soft deletes |
| `seats` | Individual seats per screen (row, number, type) with soft deletes |
| `movie_shows` | A movie scheduled on a screen at a specific time, with overlap detection |
| `show_seats` | Availability matrix — one row per seat per show |
| `bookings` | Booking record per user with full status lifecycle |
| `booking_seats` | Line items — seats per booking with price snapshot at time of booking |
| `payments` | Payment attempts with Razorpay order/payment IDs and raw webhook response |
| `jobs` / `cache` | Laravel queue and cache drivers |

**Status lifecycles:**

```
show_seats:    available → locked → booked
bookings:      pending → reserved → payment_pending → confirmed / failed / cancelled
booking_seats: pending → confirmed / cancelled
payments:      initiated → captured / failed / refunded
```

---

## API Reference

### Public (no authentication required)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/register` | Create account, receive Sanctum token |
| `POST` | `/api/login` | Authenticate, receive Sanctum token |
| `POST` | `/api/webhooks/razorpay` | Razorpay webhook receiver — HMAC-SHA256 verified |
| `GET` | `/api/movies/latest` | Paginated latest movies |
| `GET` | `/api/movies/upcoming` | Upcoming movies |
| `GET` | `/api/movies/search?query=` | Search by title |
| `GET` | `/api/movies/{id}` | Single movie by TMDB ID |

### Authenticated — all roles (`auth:sanctum`)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/v1/logout` | Revoke current token |
| `GET` | `/api/v1/user` | Authenticated user profile |
| `GET` | `/api/v1/shows` | List movie shows |
| `GET` | `/api/v1/shows/{show}` | Show detail |
| `GET` | `/api/v1/shows/{show}/seats` | Real-time seat availability |
| `GET` | `/api/v1/theater` | List theaters |

### User role

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/bookings` | My bookings |
| `POST` | `/api/v1/bookings/confirm` | Create booking — locks selected seats |
| `GET` | `/api/v1/bookings/{booking}` | Booking detail |
| `DELETE` | `/api/v1/bookings/{booking}/cancel` | Cancel booking |
| `POST` | `/api/v1/payments/initiate` | Create Razorpay order for a booking |
| `POST` | `/api/v1/payments/verify` | Verify frontend payment signature |

### Admin role — prefix `/api/v1/admin`

| Resource | Endpoints |
|---|---|
| Theaters | Full CRUD — `/admin/theaters` |
| Screens | Full CRUD — `/admin/theaters/{theater}/screens` |
| Seats | Full CRUD — `/admin/theaters/{theater}/screens/{screen}/seats` |
| Movie Shows | Full CRUD — `/admin/theaters/{theater}/movie-shows` |
| Theater Owners | Assign / revoke — `/admin/theaters/{theater}/owners` |

### Owner role — prefix `/api/v1/owner`

| Resource | Endpoints |
|---|---|
| Theaters | Read + Update — `/owner/theaters` |
| Screens | Full CRUD — `/owner/theaters/{theater}/screens` |
| Seats | Full CRUD — `/owner/theaters/{theater}/screens/{screen}/seats` |
| Movie Shows | Full CRUD — `/owner/theaters/{theater}/screens/{screen}/movie-shows` |

All paginated responses include `meta` (current_page, last_page, per_page, total) and `links` (first, last, prev, next).

---

## Key Engineering Decisions

### 1. Concurrent booking prevention with pessimistic locking

Two users selecting the same seat simultaneously is a genuine race condition. An availability check followed by a write is not enough — both requests can pass the check before either writes.

`BookingService` wraps seat selection in `DB::transaction()` with `lockForUpdate()` on the targeted `show_seats` rows. The database holds an exclusive row lock until the transaction commits. A concurrent request blocks, waits, then re-reads the status and throws `SeatUnavailableException` if the seat is no longer available. This is enforced at the database level, not the application level.

MySQL 8 is a deliberate requirement for this reason — SQLite ignores `FOR UPDATE` entirely, making the lock a no-op.

---

### 2. Async seat population via Observer and Job

When a show is created, one `show_seat` row must be inserted for every active seat in that screen. Doing this synchronously blocks the HTTP response and fails atomically for all seats if anything goes wrong mid-insert.

`MovieShowObserver::created()` dispatches `PopulateShowSeatsJob` immediately and returns. The response is instant. The job runs on the queue worker and uses a single bulk `insertOrIgnore()` for all seats in one query — regardless of screen capacity.

`insertOrIgnore()` makes the job idempotent: if the queue retries after a timeout, rows that already exist are silently skipped rather than throwing a constraint violation.

The observer is registered via the `#[ObservedBy]` attribute on `MovieShow` — any code path that creates a show triggers seat population automatically, with no controller changes required.

---

### 3. Payment gateway behind an interface

`PaymentService` and controllers depend on `PaymentGatewayInterface`, not `RazorpayService` directly. The interface defines: `createOrder()`, `verifySignature()`, `verifyPaymentSignature()`.

Two immediate practical benefits: tests inject a fake implementation with no real Razorpay calls, and the gateway is swappable — adding a second payment provider means writing one new class and changing one line in `AppServiceProvider`.

---

### 4. Webhook as the authoritative payment confirmation

The Razorpay checkout produces two signals: a frontend callback after the modal closes, and a server-to-server webhook. The frontend callback can be forged. The webhook is signed with HMAC-SHA256 using a secret only Razorpay and your server hold.

`WebhookController` reads the raw request body with `$request->getContent()` before any parsing — re-encoding JSON can change byte order, breaking the signature. Verification uses `hash_equals()` for constant-time comparison, preventing timing attacks.

`PaymentService::confirmFromWebhook()` is idempotent — Razorpay's at-least-once delivery guarantee means the same event will arrive more than once. The second call detects `status === captured` and returns without writing.

---

### 5. Policy-based authorization with clean ownership traversal

All owner-facing policies extend `OwnerPolicy`, which holds a single `ownsTheater(User $user, Theater $theater): bool` method. Each policy traverses its model's relationships to reach the theater — `$screen->theater`, `$seat->screen->theater`, `$movieShow->screen->theater` — rather than reading from `request()->route()`.

This keeps policies testable outside HTTP context. `Gate::allows('update', $screen)` works identically in a controller, a job, or a test. `Screen` declares `protected $with = ['theater']`, so the traversal in `ScreenPolicy` fires zero additional queries.

---

### 6. TMDB integration with typed exceptions and DB-first strategy

`ApiClient` wraps all HTTP calls with typed exceptions: `ApiAuthException` (do not retry), `ApiConnectionException` (retry with backoff), `ApiRateLimitException` (carries `retryAfterMs` from the `Retry-After` header).

`MovieService` uses DB-first: return cached data if fresh, refetch if stale, serve stale data on API failure, only throw when both sources are unavailable. TMDB downtime does not take down the movie listing.

`MovieDTO` is the single boundary between TMDB and the application. Every TMDB field name appears once, in `MovieDTO::fromTmdb()`. If TMDB renames a field, one line changes.

---

## Installation

The API is available at `http://localhost/api`.

**Without Docker (requires local MySQL):**

```bash
composer install
cp .env.example .env
# Configure DB_* variables for your local MySQL instance
php artisan key:generate
php artisan migrate --seed

# Run in separate terminals:
php artisan serve
php artisan queue:work
php artisan schedule:work
```

**Production scheduler** — add one cron entry on the server:

```
* * * * * php artisan schedule:run
```

This single entry manages all scheduled jobs: TMDB sync and seat lock release.

---

## Environment Variables

```env
APP_KEY=                        # Run: php artisan key:generate
APP_ENV=local                   # Set to 'production' on the server
APP_DEBUG=false                 # Must be false in production

# Database — use service name 'mysql' when running via Docker
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=movie_booking
DB_USERNAME=laravel
DB_PASSWORD=secret

QUEUE_CONNECTION=database

# TMDB — themoviedb.org → Settings → API
TMDB_API_KEY=
TMDB_BASE_URL=https://api.themoviedb.org/3

# Razorpay API keys — dashboard.razorpay.com → Settings → API Keys
RAZORPAY_KEY_ID=
RAZORPAY_KEY_SECRET=
# Razorpay webhook secret — dashboard.razorpay.com → Settings → Webhooks
RAZORPAY_WEBHOOK_SECRET=

MAIL_MAILER=log                 # Change to 'resend' or SMTP for production
```

---

## Running Tests

```bash
php artisan test
```

| Test class | Coverage |
|---|---|
| `AuthTest` | Login returns token; unauthenticated requests are rejected |
| `FetchUpcomingMoviesJobTest` | DTO field mapping; batch upsert; empty page stops gracefully; malformed movie skipped without aborting batch; language code flows to API call |

`FetchUpcomingMoviesJobTest` stubs `MovieApiInterface` with Mockery and asserts against a real database — giving isolation from the HTTP layer and integration coverage of the repository and job.

---

## Seeded Test Accounts

After `php artisan migrate --seed`:

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Owner | owner@example.com | password |
| User | user@example.com | password |
