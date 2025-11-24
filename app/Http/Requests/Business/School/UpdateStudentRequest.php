<?php

namespace App\Http\Requests\Business\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'name'=>['required', 'string', 'max:255'],
            'mobile_number'=>[ 'required','string','regex:/^[1-9]\d{9,14}$/']
        ];
    }

    public function attributes(): array
    {
        return [
            'name'=>'student name',
            'mobile_number'=>'student mobile number'
        ];
    }
}
