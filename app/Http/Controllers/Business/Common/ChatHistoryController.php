<?php

namespace App\Http\Controllers\Business\Common;

use App\Http\Controllers\Controller;
use App\Models\BusinessUser;
use App\Models\User;
use App\Models\WhatsAppChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ChatHistoryController extends Controller
{
    public function index(Request $request){
        try {
            $user = Auth::user();
            $users = null;
            $is_visited = false;
            if($user->role->slug == 'school'){
                $school = $user->school;
                $visited_pages = collect(json_decode($school->visited_pages));
                $is_visited = $visited_pages->contains('chat_history');
                if(!$is_visited){
                    $visited_pages->add('chat_history');
                    $school->update([
                        'visited_pages'=>$visited_pages
                    ]);
                }
                // $users = $school->businessUsers()->with('user')->distinct('user_id')->get();
                $users = $school->businessUsers()->with('user')->get()->pluck('user')->filter()->unique('id')->values();
            }
            // dd($users);
            if($user->role->slug == 'restaurant'){
                $restaurant = $user->restaurant;
                $visited_pages = collect(json_decode($restaurant->visited_pages));
                $is_visited = $visited_pages->contains('chat_history');
                if(!$is_visited){
                    $visited_pages->add('chat_history');
                    $restaurant->update([
                        'visited_pages'=>$visited_pages
                    ]);
                }
                // $users = $restaurant->businessUsers()->with('user')->distinct('user_id')->get();
                $users = $restaurant->businessUsers()->with('user')->get()->pluck('user')->filter()->unique('id')->values();
            }
            if($user->role->slug == 'admin'){
                // $restaurant = $user->restaurant;
                $is_visited = true;
                $users = User::whereHas('whatsappChats')->with('businessUsers')->get();
            }

            if($request->ajax()){
                // dd($users);
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($user) {
                        // return view('content.business.common.chat-history.actions', ['user'=>$user]);
                        return view('content.business.common.chat-history.actions', [
                            'user'=>$user
                        ])->render();
                    })
                    ->addColumn('added_by', function ($user) {
                        $addedByList = $user->businessUsers;
                        if ($addedByList->isNotEmpty()) {
                            $addedByString = $addedByList->pluck('added_by')->unique()->implode(', ');
                        } else {
                            $addedByString = '';
                        }
                        return $addedByString;
                    })
                    // ->editColumn('updated_at', function ($user) {
                    //     return $user->whatsAppChats()->latest()->first()?->updated_at->format('d F Y, h:i A') ?? 'N/A';
                    // })
                    ->addColumn('last_chat_display', function ($user) {
                        $chat = $user->whatsAppChats()->latest()->first();
                        return $chat ? $chat->updated_at->format('d F Y, h:i A') : 'N/A';
                    })
                    ->addColumn('last_chat_timestamp', function ($user) {
                        $chat = $user->whatsAppChats()->latest()->first();
                        return $chat ? $chat->updated_at : null;
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }

            return view('content.business.common.chat-history.index', compact('is_visited'));

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function show(User $user, Request $request){
        try {
            $chat_history = WhatsAppChat::where('user_id', $user->id)
                ->orderBy('created_at')
                ->when($request->filled(['minDate', 'maxDate']),function($q) use($request){
                    $q->whereDate('created_at','>=', $request->minDate)
                        ->whereDate('created_at', '<=', $request->maxDate);
                })
                ->get();

            if($request->ajax()){
                return response()->json([
                    'data'=>$chat_history,
                ], 200);
            }
            return view('content.business.common.chat-history.show', compact('chat_history', 'user'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
