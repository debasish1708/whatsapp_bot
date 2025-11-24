<?php

namespace App\Http\Requests\Business\School;

use App\Enums\SchoolAnnouncements;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateAnnouncementRequest extends FormRequest
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
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'string', new Enum(SchoolAnnouncements::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Announcement Title',
            'description' => 'Announcement Description',
            'start_date' => 'Valid From',
            'end_date' => 'Valid To',
            'type' => 'Announcement Type',
        ];
    }
}
