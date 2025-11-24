<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\TableHourStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TableHourController extends Controller
{

    public function show(Request $request, $tableId)
    {
        try {
            $restaurant = auth()->user()->restaurant;

            $hours = $restaurant->tableHours()->get();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $hours,
                ]);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function store(TableHourStoreRequest $request)
    {
        try {
            $restaurant = auth()->user()->restaurant;

            $restaurant->tableHours()->delete(); // Clear existing hours
            $tableHour = $restaurant->tableHours()->createMany(
                array_map(function ($slot) {
                    return [
                        'time_slot' => $slot,
                    ];
                }, $request->time_slot)
            );

          return redirect()->back()->with('success', 'table hour updated  successfully');
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
