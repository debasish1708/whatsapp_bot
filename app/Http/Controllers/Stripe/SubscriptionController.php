<?php

namespace App\Http\Controllers\Stripe;

use App\Constants\WhatsAppConstants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{

  public function showForm()
  {
    return view('content.business.school.payment.subscription-payment');
  }

  // Handle checkout
  public function createSubscription(Request $request)
  {
    $user = Auth::user();
    $businessId = $user->role->slug == 'school' ? $user->school->id : $user->restaurant->id;
    $businessType = $user->role->slug == 'school' ? 'App\\Models\\School' : 'App\\Models\\Restaurant';
    $type = config('constant.subscription_plans.type');
    $priceId = config('constant.subscription_plans.price_id');
    return $user->newSubscription($type, $priceId)
      ->checkout([
        'success_url' => route('subscribe.success'),
        'cancel_url' => route('subscribe.form') . '?cancelled=1',
        'subscription_data' => [
            'metadata' => [
                'model_type' => WhatsAppConstants::METADATA_SUBSCRIPTION,
                'business_type' => $businessType,
                'business_id' => $businessId,
            ],
        ],
      ]);
  }

  // After successful payment
  public function afterSuccess(Request $request)
  {
    $request->all();
    $user = Auth::user();
    $type = config('constant.subscription_plans.type');
    $subscription = $user->subscription($type);

    // Confirm subscription was created
    if (! $subscription) {
      return redirect()->route('subscribe.form')->with('error', 'Subscription failed.');
    }
    $user->update([
      'subscription_status'=>'active'
    ]);
    logger()->info('Subscription ID: ' . $subscription->id);
    logger()->info('Plan Price ID: ' . $subscription->stripe_price);

    // Redirect to a post-subscription form/dashboard
    return redirect()->route('subscribe.form')->with('success', 'Subscription active!');
  }

  public function cancel(Request $request)
  {
    $user = Auth::user();
    $type = config('constant.subscription_plans.type');
    $user->subscription($type)->cancel();
    // $user->update([
    //   'subscription_status'=>'canceled'
    // ]);

    return redirect()->route('subscribe.form')->with('modal_success', 'Subscription cancelled. You can access until end of billing period.');
  }


  public function billingPortal(Request $request)
  {
    $user = Auth::user();

    return redirect($user->billingPortalUrl(route('school.dashboard')));
  }
}
