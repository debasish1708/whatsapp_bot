<?php

namespace App\Http\Requests\Business\Restaurant;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
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
        // dd($this->route('menu_item')->id);
        return [
            'category_id'=>['required','uuid', Rule::exists('restaurant_menu_categories', 'id')],
            'name'=>['required', 'string', 'max:18'],
            'description'=>['nullable', 'string', 'max:72'],
            'price'=>['required', 'numeric'],
            'images'=>['nullable','array'],
            'images.*'=>['nullable', 'image','mimes:png, jpg, jpeg'],
            'tags'=>['required', 'string']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        session()->flash('offcanvas', 'edit');
        session()->flash('menu_item', $this->route('menu_item')->id);
        
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
