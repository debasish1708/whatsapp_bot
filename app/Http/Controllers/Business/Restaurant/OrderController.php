<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
  public function paymentSuccess(Request $request)
  {
    $pageConfigs = ['myLayout' => 'blank'];
    info('Payment Success', $request->all());
    return view('content.business.restaurants.payment.success', compact('pageConfigs'));
  }
  public function paymentFailed(Request $request)
  {
    $pageConfigs = ['myLayout' => 'blank'];
    info('Payment Failed', $request->all());
    return view('content.business.restaurants.payment.failed', compact('pageConfigs'));
  }

  public function index(Request $request){
    try {
      $restaurant = Auth::user()->restaurant;

      $orders = $restaurant->orders()->with('user')->latest()->get();

      $visited_pages = collect(json_decode($restaurant->visited_pages));
      $is_visited = $visited_pages->contains('order');

      if(!$is_visited){
          $visited_pages->add('order');
          $restaurant->update([
              'visited_pages'=>$visited_pages
          ]);
      }

      if($request->ajax()){
        return DataTables::of($orders)
          ->addIndexColumn()
          ->addColumn('actions', function ($order) {
            return view('content.business.restaurants.orders.actions', compact('order'));
          })
          ->editColumn('created_at', function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d H:i:s');
          })
          ->rawColumns(['actions'])
          ->make(true);
      }

      return view('content.business.restaurants.orders.index',compact('is_visited'));

    } catch (\Throwable $th) {
      return redirect()->back()->with('error', $th->getMessage());
    }
  }

  public function show(RestaurantOrder $order){
    try {
      $order->load(['cart.restaurantMenuItem', 'user']);
      return view('content.business.restaurants.orders.show', compact('order'));
    } catch (\Throwable $th) {
      return redirect()->back()->with('error', $th->getMessage());
    }
  }

  public function markAsDelivered(RestaurantOrder $order){
    try {
      $order->update([
        'status'=>'delivered'
      ]);
      return response()->json([
        'message'=> __('Order marked as delivered.')
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message'=>$th->getMessage()
      ], 400);
    }
  }

  public function markAsCanceled(RestaurantOrder $order){
    try {
      $order->update([
        'status'=>'canceled'
      ]);
      return response()->json([
        'message'=> __('Order canceled successfully.')
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message'=>$th->getMessage()
      ], 400);
    }
  }
}
