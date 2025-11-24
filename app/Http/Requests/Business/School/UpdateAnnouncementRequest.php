<?php

namespace App\Http\Requests\Business\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\SchoolAnnouncements;

class UpdateAnnouncementRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'string', new Enum(SchoolAnnouncements::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Announcement Title',
            'description' => 'Announcement Description',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'type' => 'Announcement Type',
        ];
    }
}
