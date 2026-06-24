<?php

namespace App\Http\Requests\MovieShow;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'theater_id' => "required|int|exists:theaters,id",
            'screen_id' => "required|int|exists:screens,id",
            'duration' => "required|numeric",
            'price' => "required|numeric|decimal:2",
        ];
    }
}
