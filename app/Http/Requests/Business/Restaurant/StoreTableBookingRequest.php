<?php

namespace App\Http\Requests\Business\Restaurant;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\RestaurantTableReservation;

class StoreTableBookingRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'restaurant_id' => 'required|exists:restaurants,id',
            'user_id' => 'required|exists:users,id',
            'table_id' => 'required|exists:restaurant_tables,id',
            'time_slot' => 'required|exists:restaurant_table_hours,id',
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'booking_date.after_or_equal' => 'Booking date must be today or a future date.',
            'booking_end_time.after' => 'End time must be after start time.',
            'table_id.exists' => 'Selected table does not exist.',
            'restaurant_id.exists' => 'Restaurant not found.',
            'first_name.required' => 'Please enter your name.',
            'mobile_number.required' => 'Please enter your mobile number.',
            'booking_date.required' => 'Please select a booking date.',
            'booking_start_time.required' => 'Please select a start time.',
            'booking_end_time.required' => 'Please select an end time.',
        ];
    }


}
