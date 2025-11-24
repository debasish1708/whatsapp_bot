<?php

namespace App\Http\Requests\Business\School;

use App\Enums\SchoolSosAleart;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateSosAleartRequest extends FormRequest
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
