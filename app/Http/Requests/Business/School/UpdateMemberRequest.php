<?php

namespace App\Http\Requests\Business\School;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Restaurant;
use App\Models\School;
use App\Models\User;

class UpdateMemberRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'mobile_number' => [ 'required','string','regex:/^[1-9]\d{9,14}$/'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
        $data = $validator->getData();
        $mobileNumber = $data['mobile_number'] ?? null;

        $user = User::where('mobile_number', $mobileNumber)->first();

        if ($user) {
            $businessMember = $user->businessMembers()->first();

            if ($businessMember) {
                $businessName = null;

                switch ($businessMember->businessable_type) {
                    case Restaurant::class:
                        $restaurant = Restaurant::find($businessMember->businessable_id);
                        $businessName = $restaurant?->user?->name;
                        break;
                    case School::class:
                        $school = School::find($businessMember->businessable_id);
                        $businessName = $school?->user?->name;
                        break;
                }

                $message = $businessName
                ? "The user's mobile number is already registered with {$businessName}."
                : "The user's mobile number is already registered with another business.";

                $validator->errors()->add('mobile_number', $message);
            }
        }
      });
    }

    public function failedValidation(Validator $validator)
    {
        session()->flash('offcanvas', 'edit');

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
