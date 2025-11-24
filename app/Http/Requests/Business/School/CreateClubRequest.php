<?php

namespace App\Http\Requests\Business\School;

use Illuminate\Foundation\Http\FormRequest;

class CreateClubRequest extends FormRequest
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
            'name' => ['required','min:1'],
            'description' => ['required','min:1', 'max:500'],
            'meeting_time' => ['required', 'date'],
            'location' => ['required', 'string', 'min:1'],
            'contact_person' => ['required', 'string', 'max:255'],
            'contact_phone' => [ 'required','string','regex:/^[1-9]\d{9,14}$/'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'club name',
            'description' => 'club description',
            'meeting_time' => 'meeting time',
            'location' => 'location',
            'contact_person' => 'contact person',
            'contact_phone' => 'contact phone',
        ];
    }


}
