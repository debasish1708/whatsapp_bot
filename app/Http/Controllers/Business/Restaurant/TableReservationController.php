<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Dialog360\Dialog360;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\RestaurantTableReservation;
use Yajra\DataTables\DataTables;

class TableReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $restaurant = auth()->user()->restaurant;
            $visited_pages = collect(json_decode($restaurant->visited_pages));
            $is_visited = $visited_pages->contains('tables-reservation');

            if(!$is_visited){
                $visited_pages->add('tables-reservation');
                $restaurant->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            if($request->ajax()){
                // $reservations = optional(auth()->user()->restaurant)
                //             ->reservations()->with(['table', 'tableHour', 'user'])
                //             ->orderBy('created_at')
                //             ->get();
                $reservations = optional(auth()->user()->restaurant)
                            ->reservations()
                            ->with(['table', 'tableHour', 'user'])
                            ->orderByRaw("CASE
                                            WHEN status = 'pending' THEN 1
                                            WHEN status = 'confirm' THEN 2
                                            WHEN status = 'rejected' THEN 3
                                            ELSE 4
                                        END")
                            ->orderBy('booking_date', 'desc')
                            ->get();
                // info('reservations details', json_decode($reservations,JSON_PRETTY_PRINT));
                return DataTables::of($reservations)
                      ->addIndexColumn()
                      ->addColumn('table_number', function($row) {
                          return $row->table->number ?? 'N/A';
                      })
                      ->addColumn('slot_timing', function($row) {
                          return $row->tableHour ? $row->tableHour->time_slot : 'N/A';
                      })
                      ->addColumn('customer_info', function($row) {
                          return $row->customer_name . '<br><small>' . $row->user->mobile_number . '</small>';
                      })
                      ->addColumn('booking_info', function ($row) {
                            $statusBadge = ReservationStatus::badgeFrom($row->status);
                            $timeSlot = optional($row->tableHour)->time_slot ?? 'N/A';

                            return 'Date: ' . $row->booking_date->format('d-m-Y') .
                                '<br>' . $timeSlot .
                                '<br>Status: <span class="badge ' . $statusBadge['class'] . '">' . __($statusBadge['label']) . '</span>';
                        })
                      ->addColumn('booking_date_raw', function ($row) {
                        return $row->booking_date ? $row->booking_date->timestamp : null;
                      })
                      ->addColumn('actions', function($row) {
                          return view('content.business.restaurants.tables.reservations.actions', [
                              'reservation' => $row,
                      ])->render();
                      })
                      ->addColumn('capacity',function($row){
                        return $row->table->capacity ?? 'N/A';
                      })
                      ->rawColumns(['customer_info', 'booking_info', 'actions'])
                      ->make(true);
            }
            return view('content.business.restaurants.tables.reservations.index',compact('is_visited'));
        }catch(\Exception $ex){
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function accept(RestaurantTableReservation $reservation)
    {
        try {
            $reservation->update([
                'status'=> ReservationStatus::CONFIRMED->value
            ]);
            // (new Dialog360())->sendWhatsAppMessage(
            //   $reservation->user->mobile_number,
            //   "Hello {$reservation->name}, your table booking request at {$reservation->user->name} for {$reservation->booking_date} at {$reservation->tableHour->time_slot} (Table No: {$reservation->table->number}, has been approved."
            // );

            $user = User::where('mobile_number',$reservation->user->mobile_number)->first();

            // $restaurant = $reservation->restaurant()->get();

            $restaurant_table_booking_request_json = [
                'user_name' => $user->name,
                'restaurant' => $reservation->restaurant->user->name,
                'reservation_date' => Carbon::parse($reservation->booking_date)->format('d M, Y'),
                'reservation_time' => $reservation->tableHour->time_slot,
                'table' => $reservation->table->number,
                'mobile_number' => $user->mobile_number
            ];
            // info($announcement_json);
            // dispatch(new \App\Jobs\Send360TemplateMessageJob($user,'restaurant_table_booking_accept', $restaurant_table_booking_request_json, [
            //     'restaurant_id' => $restaurant->id
            // ]));
            (new Dialog360())->sendTemplateWhatsAppMessage($user->mobile_number,'restaurant_table_booking_accept',$user->language_code,$restaurant_table_booking_request_json);

            return response()->json([
                'message'=>__('Reservation is Confirmed.')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
            'message'=>$th->getMessage()
            ]);
        }
    }

    public function reject(RestaurantTableReservation $reservation)
    {
        try {
            $reservation->update([
                'status'=> ReservationStatus::REJECTED->value
            ]);
            // (new Dialog360())->sendWhatsAppMessage(
            //   $reservation->user->mobile_number,
            //   "Hello {$reservation->name}, your table booking request at {$reservation->user->name} for {$reservation->booking_date} at {$reservation->tableHour->time_slot} (Table No: {$reservation->table->number}, has been reject."
            // );

            $user = User::where('mobile_number',$reservation->user->mobile_number)->first();

            // $restaurant = $reservation->restaurant()->get();

            $restaurant_table_booking_request_json = [
                'user_name' => $reservation->user->name,
                'restaurant' => $reservation->restaurant->user->name,
                'reservation_date' => $reservation->booking_date,
                'reservation_time' => $reservation->tableHour->time_slot,
                'table' => $reservation->table->number,
                'mobile_number' => $reservation->restaurant->mobile_number
            ];
            // info('restaurant_table_booking_request_json', $restaurant_table_booking_request_json);
            // info($announcement_json);
            // dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'restaurant_table_booking_reject', $restaurant_table_booking_request_json, [
            //     'restaurant_id' => $restaurant->id
            // ]));
            (new Dialog360())->sendTemplateWhatsAppMessage($user->mobile_number,'restaurant_table_booking_reject',$user->language_code,$restaurant_table_booking_request_json);

            return response()->json([
                'message'=>__('Reservation is Rejected.')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
            'message'=>$th->getMessage()
            ]);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RestaurantTableReservation $reservation)
    {
        try {
            $reservation->delete();
            return response()->json([
                'message'=>__('Table Reservation Deleted Successfully'),
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message'=>__('Something went wrong.'),
            ], 200);
        }
    }
}
