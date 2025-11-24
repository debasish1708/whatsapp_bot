<?php

namespace App\Http\Controllers\Business\form;

use App\Enums\SchoolSosAleart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\form\StoreSosAleartRequest;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolSosAleartController extends Controller
{
   /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        try{
            $school = School::find($id);
            if(!$school){
                abort(404);
            }
            $pageConfigs = ['myLayout' => 'blank'];
            return view('content.business.school.sos-alerts.form',[
                'pageConfigs' => $pageConfigs,
                'school' => $school
            ]);
        }catch(\Exception $ex){
            abort(404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSosAleartRequest $request)
    {
        try {
            $data = $request->validated();
            $school = School::find($data['school_id']);
            $sos = $school->sosAlerts()->create([
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type']
            ]);

            $users = User::where('is_verified', true)
            ->whereHas('businessUsers', function ($q) use ($school) {
                $q->where('businessable_type', School::class)
                ->where('businessable_id', $school->id)
                ->where('added_by', '!=', 'search');
            })
            ->get();

            if (!$users->isEmpty()) {
            //   $typeEnum = SchoolSosAleart::from($sos->type);
              $announcement_json = [
                'school_name' => $school->user->name,
                'title' => $sos->title,
                'description' => $sos->message,
                'type' => SchoolSosAleart::safeFrom($sos->type) ?? '',
             ];
              dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'alert_notification', $announcement_json, [
                'sos_id' => $sos->id
             ]));
            }

            return back()->with('modal_success','Sos Aleart Created Successfully');
        }catch(\Exception $ex){
            return redirect()->back()->with('modal_error', 'Something Went wrong!');
        }
    }
}
