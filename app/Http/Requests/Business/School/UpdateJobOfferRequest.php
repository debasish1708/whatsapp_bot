<?php

namespace App\Http\Requests\Business\School;

use App\Enums\SchoolJobOffer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateJobOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->slug==='school';
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
            'description' => ['required', 'string', 'max:500'],
            'salary' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email:dns', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'expiry_date' => ['required', 'date'],
            'status' => ['required', 'string', new Enum(SchoolJobOffer::class)],
        ];
    }

    public function attributes(){
        return [
            'position' => 'Position',
            'description' => 'Description',
            'salary' => 'Salary',
            'location' => 'Location',
            'contact_email' => 'Contact Email',
            'contact_number' => 'Phone Number',
            'expiry_date' => 'Expiry Date',
            'status' => 'Status',
        ];
    }
}
