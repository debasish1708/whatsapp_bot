<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PendingAccountController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'all');

        try {
            if ($request->ajax()) {
                // Build a single base query
                $query = User::with(['role'])
                    ->where('status', 'pending')
                    ->whereHas('role', function ($q) use ($type) {
                        if ($type === 'school') {
                            $q->where('slug', 'school');
                        } elseif ($type === 'restaurant') {
                            $q->where('slug', 'restaurant');
                        } elseif($type === 'museum'){
                            $q->where('slug', 'museum');
                        } elseif($type === 'hotel'){
                            $q->where('slug', 'hotel');
                        } else {
                            $q->whereIn('slug', ['school', 'restaurant']);
                        }
                    });

                // Eager load relationship based on type
                if ($type === 'school') {
                    $query->with('school');
                } elseif ($type === 'restaurant') {
                    $query->with('restaurant');
                } else {
                    $query->with(['school', 'restaurant']);
                }

                $data = $query->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('logo', function ($user) {
                        if ($user->role->slug === 'school') {
                            $logo = $user->school && $user->school->logo ? $user->school->logo : null;
                            return "<img src='" . ($logo ?? asset('assets/img/placeholder.jpg')) . "' alt='' width='90' height='90'/>";
                        }
                        else if ($user->role->slug === 'restaurant') {
                            $logo = $user->restaurant && $user->restaurant->logo ? $user->restaurant->logo : null;
                            return "<img src='" . ($logo ?? asset('assets/img/restaurant_placeholder.png')) . "' alt='' width='90' height='90'/>";
                        }
                        return "<img src='" . asset('assets/img/placeholder-restaurant.png') . "' alt='' width='90' height='90'/>";
                    })
                    ->editColumn('address', function ($user) {
                        return $user->role->slug === 'school'
                            ? ($user->school->address ?? 'N/A')
                            : ($user->restaurant->address ?? 'N/A');
                    })
                    ->addColumn('name', fn($user) => $user->name)
                    ->addColumn('profile_status', function ($user) {
                        if ($user->role->slug === 'school') {
                            return $user->school && $user->school->is_profile_completed ? 'Completed' : 'Incomplete';
                        }

                        if ($user->role->slug === 'restaurant') {
                            return $user->restaurant && $user->restaurant->is_profile_completed ? 'Completed' : 'Incomplete';
                        }

                        return 'Incomplete';
                    })
                    ->addColumn('status', fn($user) => $user->status ?? 'N/A')
                    ->addColumn('actions', function ($user) {
                        $roleSlug = $user->role->slug;
                        if ($roleSlug === 'school' && $user->school) {
                            return view('content.admin.schools.actions', ['school' => $user->school])->render();
                        }
                        if ($roleSlug === 'restaurant' && $user->restaurant) {
                            return view('content.admin.restaurants.actions', ['restaurant' => $user->restaurant])->render();
                        }
                        return '';
                    })
                    ->editColumn('created_at', fn($user) => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'))
                    ->rawColumns(['logo', 'actions'])
                    ->make(true);
            }

            return view('content.admin.pending.index');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'No pending accounts found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
