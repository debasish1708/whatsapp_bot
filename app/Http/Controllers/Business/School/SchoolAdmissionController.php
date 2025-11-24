<?php

namespace App\Http\Controllers\Business\School;

use App\Actions\DatabaseAction;
use App\Constants\WhatsAppConstants;
use App\Dialog360\Dialog360;
use App\Enums\SchoolAdmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\SchoolAdmissionStoreRequest;
use App\Models\School;
use App\Models\SchoolAdmission;
use App\Models\User;
use App\Models\WhatsAppChat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Yajra\DataTables\DataTables;

class SchoolAdmissionController extends Controller
{
      protected $dialog360Object;

      protected $databaseAction;
      public function __construct()
      {
        $this->dialog360Object = new Dialog360();
        $this->databaseAction = new DatabaseAction();
      }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{

            $user=auth()->user();
            $school = $user->school;

            $visited_pages = collect(json_decode($school->visited_pages));
            $is_visited = $visited_pages->contains('admissions');

            if(!$is_visited){
                $visited_pages->add('admissions');
                $school->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            if($request->ajax()){
              $user=auth()->user();
              $query = $school
                      ->admissions()
                      ->with(['school', 'payments'])
                      ->when($request->exists('status') && $request->status != 'all', function ($query) use($request) {
                            $query->where('status', $request->status);
                      })
                      ->latest();

                return DataTables::of($query)
                        ->addIndexColumn()
                        ->addColumn('name', function($row){
                            $name = $row->first_name.' '.$row->last_name;
                            return $name;
                        })
                        ->addColumn('payment', function($row){
                            $latestPayment = $row->payment_status;
                            if ($latestPayment) {
                                return match ($latestPayment) {
                                    'paid' => '<span class="badge bg-success bg-opacity-10 text-success">' . __('Paid') . '</span>',
                                    'failed' => '<span class="badge bg-danger bg-opacity-10 text-danger">' . __('Failed') . '</span>',
                                    'refunded' => '<span class="badge bg-info bg-opacity-10 text-info">' . __('Refunded') . '</span>',
                                    default => '<span class="badge bg-warning bg-opacity-10 text-warning">' . __('Pending') . '</span>',
                                };
                            }
                            return '<span class="badge bg-warning bg-opacity-10 text-warning">' . __('N/A') . '</span>';
                        })
                        ->editColumn('created_at',function($admission){
                          return Carbon::parse($admission->created_at)->format("Y-m-d H:i:s");
                        })
                        ->addColumn('actions', function($row){
                            return view('content.business.school.form-applications.actions', compact('row'));
                        })
                        ->rawColumns(['actions','payment'])
                        ->make(true);
            }
            return view('content.business.school.form-applications.index', compact('is_visited'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
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
    public function store(SchoolAdmissionStoreRequest $request)
    {
        try{
            $user = User::where('mobile_number', $request->mobile_number)
                        ->first();
            $locale = $user->language_code == WhatsAppConstants::LANGUAGE_CS ? 'cs' : 'en';

                $school = School::findOrFail($request->school_id);
                $admission=SchoolAdmission::create([
                  'school_id' => $request->school_id,
                  'user_id' => $user->id,
                  'first_name' => $request->first_name,
                  'last_name' => $request->last_name,
                  "parents_name" => $request->parents_name,
                  'email' => $request->email,
                  'mobile_number' => $request->mobile_number,
                  "parent_mobile_number" => $request->parent_mobile_number,
                  'date_of_birth' => $request->date_of_birth,
                  'gender'=> $request->gender,
                  "grade" => $request->grade,
                  'address' => $request->address,
                  'city' => $request->city,
                ]);

                if($school->requires_payment)
                {
                  Stripe::setApiKey(config('cashier.secret')); // same as Cashier's key

                  $checkout = Session::create([
                    'customer' => $user->stripe_id, // Cashier manages this
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                      'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $school->admission_fee * 100, // Stripe expects the amount in cents
                        'product_data' => [
                          'name' => 'Admission Fee - '.$school->name,
                        ],
                      ],
                      'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('school.admission.payment.success') . '?lang=' . $locale,
                    'cancel_url'  => route('school.admission.payment.failed') . '?lang=' . $locale,
                    'locale' => $locale,
                    'metadata' => [
                      'type' => WhatsAppConstants::METADATA_TYPE_SCHOOL_ADMISSION,
                      'admission_id' => $admission->id,
                      'user_id' => $user->id,
                      'school_id' => $school->id,
                    ],
                  ]);

                  $paymentUrl = $checkout->url;
                  $paymentCode = "/". Str::after($paymentUrl, '/pay/');

                  $bodyParams = [
                    $user->name,
                    $school->admission_fee . '  CZK',
                  ];
                  $buttonParams = [
                    $paymentCode
                  ];
                  $templateName = 'admission_payment';

                  $this->dialog360Object->sendTemplateWhatsAppMessage($user->mobile_number, $templateName, $user->language_code,$bodyParams,$buttonParams);
                  $rendered = $this->dialog360Object->renderTemplate($templateName, $bodyParams);
                  $this->databaseAction->storeConversation($user, "", $rendered);

                }
            $message = $user->language_code == WhatsAppConstants::LANGUAGE_CS ? 'Formulář byl úspěšně odeslán. Odkaz na platbu byl odeslán na váš WhatsApp.' :
                                  'Form submitted successfully. Payment link has been sent to your WhatsApp.';
            return back()->with('modal_success', $message);
        }catch(\Exception $e){
            return redirect()->back()->with('modal_error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      try{
        $school = School::where('id', $id)->first();
        if (!$school) {
            abort(404);
        }
        $pageConfigs = ['myLayout' => 'blank'];
        $lang = request()->query('lang', 'english');
        return view('content.business.school.admission.form', compact('school', 'pageConfigs', 'lang'));
      } catch(\Exception $e){
        abort(404);
      }
    }

    public function showAdmissionData(SchoolAdmission $school_admission){
      try {
        return response()->json([
          'first_name'=>$school_admission->first_name,
          'last_name'=>$school_admission->last_name,
          'email'=>$school_admission->email,
          'mobile_number'=>$school_admission->mobile_number,
          'dob'=>$school_admission->date_of_birth,
          'gender'=>$school_admission->gender,
          'address'=>$school_admission->address,
          'city'=>$school_admission->city,
          'status'=>$school_admission->status,
          'payment_status'=>$school_admission->payment_status,
          'created_at'=>Carbon::parse(time: $school_admission->created_at)->format('Y-m-d H:i:s'),
        ],200);
      } catch (\Throwable $th) {
        return response()->json([
          'message'=>$th->getMessage()
        ], 400);
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
    public function destroy(string $id)
    {
        //
    }

    public function paymentSuccess(Request $request)
    {
      $pageConfigs = ['myLayout' => 'blank'];
      info('Payment Success', $request->all());
      $lang = $request->query('lang', 'en');
        return view('content.business.school.payment.success', compact('pageConfigs', 'lang'));
    }
    public function paymentFailed(Request $request)
    {
    $pageConfigs = ['myLayout' => 'blank'];
      info('Payment Failed', $request->all());
        return view('content.business.school.payment.failed', compact('pageConfigs'));
    }

    public function accept(SchoolAdmission $school_admission){
      try {
        $school_admission->update([
          'status'=> SchoolAdmissionStatus::ACCEPTED->value
        ]);
        return response()->json([
          'message'=>__('Application approved successfully.')
        ]);
      } catch (\Throwable $th) {
        return response()->json([
          'message'=>$th->getMessage()
        ]);
      }
    }

    public function Reject(SchoolAdmission $school_admission){
      try {
        $school_admission->update([
          'status'=> SchoolAdmissionStatus::REJECTED->value
        ]);
        return response()->json([
          'message'=> __('Application rejected successfully.')
        ]);
      } catch (\Throwable $th) {
        return response()->json([
          'message'=>$th->getMessage()
        ]);
      }
    }

    public function Refund(SchoolAdmission $school_admission){
      try {
        if($school_admission->payment_status != 'paid'){
          return response()->json([
            'message'=> __('Payment not found or not completed.'),
          ], 400);
        }
        // Refund logic here
        // Assuming you have a method to process refunds
        $refundStatus = $this->processRefund($school_admission);

        if($refundStatus){
          $school_admission->update([
            'payment_status'=> 'refunded'
          ]);
          return response()->json([
            'message'=> __('Payment refunded successfully.')
          ]);
        } else {
          return response()->json([
            'message'=> __('Refund failed. Please try again later.'),
          ], 500);
        }
      } catch (\Throwable $th) {
        return response()->json([
          'message'=>$th->getMessage()
        ], 500);
      }
    }

    private function processRefund(SchoolAdmission $school_admission){
      try {
        Stripe::setApiKey(config('cashier.secret')); // same as Cashier's key

        $payment = $school_admission->payments()->first();
        info('Processing refund for payment', ['payment' => $payment]);
        if (!$payment || !$payment->stripe_payment_intent) {
            throw new \Exception("No valid payment found for admission ID: {$school_admission->id}");
        }

        $refund = \Stripe\Refund::create([
          'payment_intent' => $payment->stripe_payment_intent,
        ]);
        info('Refund successful', ['refund' => $refund]);

        $payment->update([
          'stripe_refund_id' => $refund->id,
        ]);

        return $refund->status === 'succeeded';
      } catch (\Exception $e) {
        info('Refund failed', ['error' => $e->getMessage()]);
        return false;
      }
    }

}
