<?php

namespace App\Http\Requests\Screen;

use App\Models\Screen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScreenRequest extends FormRequest
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
            'name' => "required|string|max:255",
            'type' => ["required", "string", Rule::in(Screen::TYPES)],
            'capacity' => "required|int|min:30|max:1000",
            'status' => "required|boolean",
        ];
    }
}
