<?php

namespace App\Http\Requests\Business\Restaurant;

use App\Enums\RestaurantOfferType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreOfferRequest extends FormRequest
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
            'title'=>['required', 'string', 'max:255'],
            'description'=>['required', 'string', 'max:1000'],
            'discount_type'=>['required', new Enum(RestaurantOfferType::class)],
            'discount'=>['required_if:discount_type,null'],
            'starts_from'=>['required', 'date', 'after_or_equal:today'],
            'ends_at'=>['required', 'date', 'after:starts_from'],
            'applicable_items'=>['required', 'array', 'min:1'],
            'applicable_items.*'=>['required', 'uuid', Rule::exists('restaurant_menu_items', 'id')]
        ];
    }

    public function failedValidation(Validator $validator)
    {
        session()->flash('offcanvas', 'add');

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
