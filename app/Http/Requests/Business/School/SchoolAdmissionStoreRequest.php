<?php

namespace App\Http\Requests\Business\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use function Symfony\Component\Translation\t;

class SchoolAdmissionStoreRequest extends FormRequest
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
            'school_id' => ['required', 'exists:schools,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'parents_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:20',Rule::exists('users', 'mobile_number')->where(function ($query) {
                $query->where('deleted_at', null);
            })],
            'parent_mobile_number' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required',Rule::in(['male','female','other'])],
            'grade' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100']
        ];
    }
}
