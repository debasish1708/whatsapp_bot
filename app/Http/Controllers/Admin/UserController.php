<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $users = User::whereHas('role', function ($query) {
                $query->where('name', 'User');
            })->get();

            if($request->ajax()){
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($user) {
                        return view('content.admin.users.actions', compact('user'));
                    })
                    ->editColumn('created_at', function ($user) {
                        return $user->created_at->diffForHumans();
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.admin.users.index');
        }catch(\Exception $e){
           return response()->json(['error' => 'Failed to fetch events'], 500);
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
    public function show(User $user)
    {
        try{
            return view('content.admin.users.show', compact('user'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to fetch Users');
        }
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
    public function destroy(User $user)
    {
        try{
            $user->delete();
            return response()->json(['success' => 'User deleted successfully'], 200);
        }catch(\Exception $e){
            return response()->json(['modal_error' => 'Failed to delete user'], 500);
        }
    }
}
