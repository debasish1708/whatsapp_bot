<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreTableRequest;
use App\Http\Requests\Business\Restaurant\UpdateTableRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $restaurant = auth()->user()->restaurant;

            $visited_pages = collect(json_decode($restaurant->visited_pages));
            $is_visited = $visited_pages->contains('tables');

            if(!$is_visited){
                $visited_pages->add('tables');
                $restaurant->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            $tableHours = $restaurant->tableHours()->where('deleted_at', null)->get();
            if($request->ajax()){
                $tables = $restaurant->tables()->orderBy('created_at','desc');
                return DataTables::of($tables)
                      ->addIndexColumn()
                      ->addColumn('actions',function($row){
                        return view('content.business.restaurants.tables.actions', [
                            'table' => $row,
                        ])->render();
                      })
                      ->rawColumns(['actions'])
                      ->make(true);
            }
            // For non-ajax, return the view
            return view('content.business.restaurants.tables.index', [
                'tableHours' => $tableHours,
                'is_visited' => $is_visited
            ]);
        }catch(\Exception $ex){
            return response()->json(['error' => $ex->getMessage()], 500);
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
    public function store(StoreTableRequest $request)
    {
        try {
            $restaurant = auth()->user()->restaurant;
            foreach($restaurant->tables as $table){
                if(strtolower($table->number) == strtolower($request->number)){
                    return redirect()->back()->with('error','Table number already exists');
                }
            }
            $table = RestaurantTable::create([
                'restaurant_id' => optional(auth()->user()->restaurant)->id,
                'number' => $request->number,
                'capacity' => $request->capacity,
            ]);
            return redirect()->back()->with('success', 'table create successfully');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error',__('Something went wrong.'));
        }
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
    public function edit(RestaurantTable $table)
    {
        try{
            return response()->json(['data' => $table]);
        }catch(\Exception $ex){
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTableRequest $request, $id)
    {
        try {
            $table = RestaurantTable::findOrFail($id);
            $table->update([
                'number' => $request->number,
                'capacity' => $request->capacity,
            ]);
            return redirect()->back()->with('success','Table update sucessfully');
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RestaurantTable $table)
    {
        try {
            $table->delete();
            return response()->json([
                'message'=>__('Table Deleted Successfully'),
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message'=>__('Something went wrong.'),
            ], 200);
        }
    }
}
