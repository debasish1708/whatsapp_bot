<?php

namespace App\Http\Controllers\Business\Common;

use App\Constants\WhatsAppConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\StoreJobApplicationRequest;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(string $id){
        try{
            $jobOffer = JobOffer::where('id',$id)->first();
            $business = $jobOffer->businessable;
            if (!$jobOffer) {
              abort(404);
            }
            $pageConfigs = ['myLayout' => 'blank'];
            $lang = request()->query('lang', 'english');
            return view('content.business.common.job.form', [
                'job_offer' => $jobOffer,
                'pageConfigs' => $pageConfigs,
                'business' => $business,
                'school' => $business,
                'lang' => $lang,
            ]);
        }catch(\Exception $e){
            // return response()->json(['error' => $e->getMessage()], 500);
            abort(404);
        }
    }
    public function store(StoreJobApplicationRequest $request)
    {
        try{
            $data = $request->validated();
            $user = User::where('mobile_number', $request->mobile_number)
                        ->first();
            $message = $user->language_code == WhatsAppConstants::LANGUAGE_CS ? 'Přihláška byla úspěšně odeslána!' : 'Application submitted successfully!';
            $data['user_id'] = $user->id;
            $jobOffer = JobOffer::find($request->job_offer_id);
            if (isset($data['resume'])) {
                $path = $data['resume']->store('public/job_application/resume', 's3');
                $data['resume'] = basename($path);
            }
            $jobOffer->jobApplications()->create($data);
            return redirect()->back()->with('modal_success', $message);
        }catch(\Exception $e){
            return redirect()->back()->with('modal_error', 'Application submission failed!');
        }
    }
}
