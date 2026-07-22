<?php

namespace App\Http\Requests\MovieShow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMovieShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => "required|int|exists:movies,id",
            'screen_id' => "required|int|exists:screens,id",
            'duration' => "required|numeric",
            'price' => "required|numeric|decimal:2",
            'scheduled_at' => "required|date|after_or_equal:now",
        ];
    }

    // public function after() : array {
    //     return [
    //         function (Validator $validator) {
    //             # Check is show timing overlapping
    //         },

    //         function (Validator $validator) {
    //             # Check is show duration more than movie
    //         },

    //         function (Validator $validator) {
    //             # Check is screen related to the theater
    //         },
    //     ];
    // }
}
