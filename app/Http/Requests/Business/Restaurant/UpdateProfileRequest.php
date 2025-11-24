<?php

namespace App\Http\Requests\Business\Restaurant;

use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        // dd($_REQUEST);
        return [
            'restaurant_name' => ['required', 'string', 'max:255'],
            'restaurant_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg'],
            'restaurant_categories' => ['required', 'array', 'min:1'],
            'restaurant_categories.*' => ['required', 'uuid', Rule::exists('restaurant_categories', 'id')],
            'cuisine_type' => ['required', 'array', 'min:1'],
            'cuisine_type.*' => ['required', 'uuid', Rule::exists('cuisines', 'id')],
            'address' => ['required', 'string', 'max:1000'],
            'address_link' => ['nullable', 'url'],
            'city' => ['required', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'regex:/^[1-9]\d{9,14}$/'],
            'country' => ['required', 'string', 'max:255'],
            'timings' => ['required', 'array'],
            'timings.*.day' => ['required', 'string'],
            'timings.*.start_time' => ['nullable', 'date_format:H:i'],
            'timings.*.end_time' => ['nullable', 'date_format:H:i'],
            'timings.*.is_closed' => ['nullable', 'boolean'],
            'sustainabilities'=>['required','array','min:1'],
            'sustainabilities.*'=>['required', 'uuid', Rule::exists('sustainabilities','id')],
            'accessibilities'=>['nullable', 'array'],
            'accessibilities.*'=>['nullable', 'uuid', Rule::exists('accessibilities','id')],
        ];
    }
    // 'sustainabilities.*'=>['required', 'uuid', Rule::exists('sustainabilities','id')],
    // 'accessibilities.*'=>['nullable', 'uuid', Rule::exists('accessibilities','id')],

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->input('timings', []) as $index => $timing) {
                $start = $timing['start_time'] ?? null;
                $end = $timing['end_time'] ?? null;
                $closed = $timing['is_closed'] ?? false;
                // dd($closed);
                if ($closed) {
                    if($start){
                        $validator->errors()->add("timings.$index.start_time", 'Start time is required when the restaurant is not closed.');
                    }
                    continue;
                }
                if (!$start) {
                    $validator->errors()->add("timings.$index.start_time", 'Start time is required.');
                }
                if ($start && !$end) {
                    $validator->errors()->add("timings.$index.end_time", 'End time is required when start time is given.');
                }
                if ($start && $end && $end <= $start) {
                    $validator->errors()->add("timings.$index.end_time", 'End time must be after start time.');
                }
            }
        });
    }

    public function attributes(){
        return [
            'timings.*.day'=> 'day',
            'timings.*.start_time'=>'start_time',
            'timings.*.end_time'=>'end_time',
            'timings.*.is_closed'=>'is_closed'
        ];
    }

}
