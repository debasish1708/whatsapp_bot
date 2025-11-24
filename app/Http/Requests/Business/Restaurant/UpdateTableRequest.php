<?php

namespace App\Http\Requests\Business\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => 'required|string|max:5',
            'capacity' => 'required|integer|min:1|max:20',
        ];
    }

    public function attributes()
    {
        return [
            'number' => 'The Table Number',
            'capacity' => 'The capacity'
        ];
    }
}
