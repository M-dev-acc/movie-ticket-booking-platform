<?php

namespace App\Http\Requests\Theater;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTheaterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:8', 'max:255', 'regex:/^[A-Za-z0-9() ]+$/s'],
            'address' => ['required', 'string', 'regex:/^[A-Za-z0-9() ,\-]+$/s'],
            'status' => ['required', 'boolean']
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => "Validation failed",
            'error' => $validator->errors(),
        ], 422));
    }
}
