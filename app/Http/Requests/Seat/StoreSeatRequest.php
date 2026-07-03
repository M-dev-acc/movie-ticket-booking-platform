<?php

namespace App\Http\Requests\Seat;

use App\Models\Seat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSeatRequest extends FormRequest
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
            'seats' => ["required", "array", "min:1"],
            'seats*.row' => ["required", "string", "max:2"],
            'seats*.number' => ["required", "integer", "max:99"],
            'seats*.type' => ["required", Rule::in(Seat::TYPES)],
            'seats*.is_active' => ["required", "boolean"],
        ];
    }
}
