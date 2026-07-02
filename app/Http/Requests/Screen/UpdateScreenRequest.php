<?php

namespace App\Http\Requests\Screen;

use App\Models\Screen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScreenRequest extends FormRequest
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
            'name' => "sometimes|filled|string|max:255",
            'type' => ["sometimes", "filled", "string", Rule::in(Screen::TYPES)],
            'capacity' => "sometimes|integer|min:30|max:1000",
            'status' => "sometimes|boolean",
        ];
    }
}
