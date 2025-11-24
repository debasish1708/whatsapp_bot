<?php

namespace App\Http\Requests\Business\School;

use App\Enums\SchoolEvents;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateEventCalenderRequest extends FormRequest
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
            'title' => ['required','min:1'],
            'type' => ['required','min:1', new Enum(SchoolEvents::class)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Event Title',
            'type' => 'Event Type',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'description' => 'Event Description',
        ];
    }

}
