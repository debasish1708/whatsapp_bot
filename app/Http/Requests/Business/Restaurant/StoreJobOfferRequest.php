<?php

namespace App\Http\Requests\Business\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
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
            'position' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'salary' => ['required', 'numeric'],
            'location' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email:dns', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'expiry_date' => ['required', 'date', 'after:today'],
            'status' => ['required', 'string', 'in:active,inactive,expired'],
        ];
    }
}
