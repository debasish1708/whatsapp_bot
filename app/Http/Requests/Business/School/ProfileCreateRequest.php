<?php

namespace App\Http\Requests\Business\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileCreateRequest extends FormRequest
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
            'user'=> ['required', 'array', 'min:1'],
            'school' => ['required', 'array', 'min:1'],
            'school.requires_payment' => ['required',Rule::in(['true', 'false'])],
            'school.admission_fee' => [
                Rule::requiredIf(function () {
                    return $this->input('school.requires_payment') == 'true';
                }),
                'integer',
                'min:1'
            ],
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => ['required', 'string', 'email', 'max:255'],
//            'school.category_id' => ['required', Rule::exists('school_categories', 'id' )],
            'school.categories' => ['required', 'array'],
            'school.categories.*' => ['required', 'uuid', Rule::exists('school_categories', 'id')],
            'school.address' => ['required', 'string', 'max:255'],
            'school.address_link' => ['nullable', 'url'],
            'school.city' => ['required', 'string', 'max:255'],
            'school.pincode' => ['nullable', 'string', 'max:255'],
            'school.mobile_number' => ['required', 'string', 'max:255'],
            'school.country' => ['required', 'string', 'min:1','max:255'],
            'school.services' => ['required', 'array', 'min:1'],
            'school.services.*' => ['string', 'max:255'],
            'school.logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user.name' => 'School Name',
            'user.email' => 'Email Address',
            'school.requires_payment' => 'Requires Payment',
            'school.admission_fee' => 'Admission Fee',
            'school.categories' => 'School Category',
            'school.address' => 'Address',
            'school.address_link' => 'Address Link',
            'school.city' => 'City',
            'school.pincode' => 'Pincode',
            'school.mobile_number' => 'Mobile Number',
            'school.country' => 'Country',
            'school.services' => 'Services Offered',
            'school.logo' => 'Logo Image',

        ];
    }
}
