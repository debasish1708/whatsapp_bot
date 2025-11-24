<?php

namespace App\Http\Requests\Business\School;

use App\Enums\SchoolPsychologicalOfficeHour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreatePsychologicalSupportRequest extends FormRequest
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
            'mobile_number' => [ 'required','string','regex:/^[1-9]\d{9,14}$/'],
            'office_hours' => ['required','array','min:1', 'max:7'],
            'office_hours.*.day' => ['required','string', new Enum(SchoolPsychologicalOfficeHour::class)],
            'office_hours.*.start_time' => ['nullable', 'date_format:H:i'],
            'office_hours.*.end_time'   => ['nullable', 'date_format:H:i'],
            'office_hours.*.is_closed'   => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'mobile_number' => __('Mobile Number'),
            'office_hours' => 'Office Hours',
            'office_hours.*.day' => 'Day',
            'office_hours.*.start_time' => 'Start Time',
            'office_hours.*.end_time' => 'End Time',
        ];
    }
}
