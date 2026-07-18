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
    )
    {
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
            'user_id' => "required|int|exists:users,id",
            'show_id' => "required|int|exists:movie_shows,id",
            'seats' => "required|array",
            'seats.*id' => "required|interger|exists:seats,id",
        ];
    }

    public function after() : array {
        return [
            function (Validator $validator) {
                if($this->bookingService
                    ->areSeatsFromSameTheater(
                        $this->input('seats')
                            ->pluck('id')
                            ->toArray(),
                        $this->get('theater'))
                ){
                    $validator->errors()
                        ->add(
                        'seats',
                        'Invalid selected seats');
                }
            },
            function (Validator $validator) {
                if($this->bookingService
                    ->areSeatsAvailable(
                        $this->input('seats')
                        ->pluck('id'))
                ){
                    $validator->errors()
                        ->add(
                        'seats',
                        'Selected seat(s) are not available.');
                }
            },
        ];
    }

}

