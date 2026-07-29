<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function __construct(
        private BookingService $bookingService,
    ) {
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Booking::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'show_id' => "required|integer|exists:movie_shows,id",
            'seats' => "required|array|min:1",
            'seats.*.id' => "required|integer|exists:seats,id",
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!$this->bookingService
                    ->areSeatsAvailable(
                        $this->integer('show_id'),
                        collect($this->input('seats'))
                            ->pluck('id')
                            ->toArray()
                    )
                ) {
                    $validator->errors()
                        ->add(
                            'seats',
                            'Selected seat(s) are not available.'
                        );
                }
            },
        ];
    }

}
