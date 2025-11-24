<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Dialog360\Dialog360;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreTableBookingRequest;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\RestaurantTableHour;
use App\Models\RestaurantTableReservation;
use App\Models\User;

class TableBookingController extends Controller
{
    public function showBookingForm(string $restaurat, string $user)
    {
        try{
            $restaurant = Restaurant::findOrFail($restaurat);
            $user = User::findOrFail($user);
            $tables = $restaurant->tables()->get();
            $tableHours = $restaurant->tableHours->sortBy(function($item) {
              $startTime = explode(' - ', $item['time_slot'])[0];
              return \DateTime::createFromFormat('h:i A', $startTime)->getTimestamp();
            });


            if(!$restaurant || !$tables || !$tableHours ){
                abort(404);
            }
            $pageConfigs = ['myLayout' => 'blank'];
            $lang = request()->query('lang', 'english');
            return view('content.business.restaurants.tables.booking.form', [
                'restaurant' => $restaurant,
                'user' => $user,
                'tables' => $tables,
                'tableHours' => $tableHours,
                'pageConfigs' => $pageConfigs,
                'lang' => $lang
            ]);
        }catch(\Exception $ex){
            abort(404);
        }
    }

    public function store(StoreTableBookingRequest $request)
    {
        try {
            $reservation = RestaurantTableReservation::create([
                'restaurant_id' => $request->restaurant_id,
                'restaurant_table_id' => $request->table_id,
                'restaurant_table_hour_id' => $request->time_slot,
                'user_id' => $request->user_id,
                'customer_name' => $request->name,
                'mobile_number' => $request->mobile_number,
                'booking_date' => $request->booking_date,
            ]);
            $user  = User::find($request->user_id);
            $restaurant = Restaurant::findOrFail($request->restaurant_id);
            $time = RestaurantTableHour::findOrFail($request->time_slot);
            $table = RestaurantTable::findOrFail($request->table_id);
            // (new Dialog360())->sendWhatsAppMessage(
            //   $user->mobile_number,
            //   "Hello {$user->name}, your table booking request at {$restaurant->user->name} for {$reservation->booking_date} at {$time->time_slot} (Table No: {$table->number}, Guests: {$table->capacity}) has been received. The restaurant will contact you shortly to confirm your booking."
            // );
            info('user info'. json_encode($user,JSON_PRETTY_PRINT));
            $restaurant_name = $restaurant->user->name;

            $restaurant_table_booking_request_json = [
                'user_name' => $user->name,
                'restaurant' => $restaurant_name,
                'reservation_date' => $reservation->booking_date,
                'reservation_time' => $time->time_slot,
                'table' => $table->number,
                'guests' => $table->capacity,
                'mobile_number' => $restaurant->mobile_number,
            ];
            info('Restaurant tabe booking info '. json_encode($restaurant_table_booking_request_json,JSON_PRETTY_PRINT));
            // dispatch(new \App\Jobs\Send360TemplateMessageJob($user,'restaurant_table_booking_request', $restaurant_table_booking_request_json, [
            //     'restaurant_id' => $restaurant->id
            // ]));
             (new Dialog360())->sendTemplateWhatsAppMessage($user->mobile_number,'restaurant_table_booking_request',$user->language_code,$restaurant_table_booking_request_json);

            return redirect()->back()->with('modal_success', 'Table booked successfully! We will contact you shortly.');
        } catch (\Exception $ex) {
            return redirect()->back()
                ->withErrors(['error' => 'Table booking failed. Please try again.'])
                ->withInput();
        }
    }
}
