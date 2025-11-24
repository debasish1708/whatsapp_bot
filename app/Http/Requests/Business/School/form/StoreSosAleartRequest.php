<?php

namespace App\Http\Requests\Business\School\form;

use App\Enums\SchoolSosAleart;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreSosAleartRequest extends FormRequest
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
            'school_id' => ['required',Rule::exists('schools', 'id')],
            'title' => ['required','string','min:1'],
            'message' => ['required','string','max:255'],
            'type' => ['required','string',new Enum(SchoolSosAleart::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'title',
            'message' => 'message',
            'type' => 'type'
        ];
    }
}
