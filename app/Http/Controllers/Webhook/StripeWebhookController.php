<?php

namespace App\Http\Controllers\Webhook;

use App\Constants\WhatsAppConstants;
use App\Dialog360\Dialog360;
use App\Enums\SchoolAdmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\pages\Page2;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\RestaurantCart;
use App\Models\RestaurantOrder;
use App\Models\School;
use App\Models\SchoolAdmission;
use App\Models\Webhook;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Stripe\Subscription as StripeSubscription;
use Laravel\Cashier\Http\Controllers\WebhookController;

class StripeWebhookController extends WebhookController
{
  public function handleWebhook(Request $request)
  {
//    Webhook::create([
//      'vendor' => 'stripe',
//      'webhook_id' => 'stripe_webhook',
//      'header' => json_encode($request->headers->all()),
//      'payload' => json_encode($request->all()),
//    ]);
    return parent::handleWebhook($request); // still uses Cashier’s logic
  }
  public function handleCheckoutSessionCompleted($payload)
  {
    $session = $payload['data']['object'];
    info('Stripe Checkout Session Completed: ' , $payload);
    $metadata = $session['metadata'] ?? [];
    if (isset($metadata['type']) && $metadata['type'] == WhatsAppConstants::METADATA_TYPE_SCHOOL_ADMISSION) {
      // Not an admission payment session, ignore
      $this->handleSchoolAdmission($session);
    }
    elseif(isset($metadata['type']) && $metadata['type'] == WhatsAppConstants::METADATA_TYPE_RESTAURANT_ORDER) {
      // Handle subscription payment
      $this->handleRestaurantOrder($payload);
    }
    elseif(isset($metadata['type']) && $metadata['type'] == WhatsAppConstants::METADATA_SUBSCRIPTION){
      $this->handleCustomerSubscriptionCreated($payload);
    }
    else {
      \Laravel\Prompts\info('Stripe Checkout Session Completed for Unknown Type', $session);
    }

    return response()->json(['status' => 'ok']);
  }

  public function handleRestaurantOrder($payload)
  {
    $session = $payload['data']['object'];
    $metadata = $session['metadata'] ?? [];
    $restaurantId = $metadata['restaurant_id'] ?? null;
    $restaurantCartIds = isset($metadata['restaurant_cart_ids']) ? json_decode($metadata['restaurant_cart_ids'], true) : null;
    $userId = $metadata['user_id'] ?? null;

    if ($restaurantId && $restaurantCartIds) {



      $restaurantCart = RestaurantCart::query()
        ->whereIn('id',$restaurantCartIds)
        ->where('restaurant_id', $restaurantId)
        ->where('user_id', $userId)
        ->where('is_order_placed', false)
        ->get();


      if ($restaurantCart) {
        $user  = \App\Models\User::find($userId);
        if ($session['payment_status'] == config('constant.stripe_checkout_status.paid')) {
          $amount = $session['amount_total'] / 100;
          $restaurantOrder=  RestaurantOrder::create([
            'user_id' => $userId,
            'restaurant_id' => $restaurantId,
            'total_amount' => $amount,
            'status' => 'pending',
            'payment_status' => 'paid',
          ]);

          // Update restaurant cart items
          RestaurantCart::query()
            ->whereIn('id',$restaurantCartIds)
            ->update([
              'is_order_placed' => true,
              'restaurant_order_id' => $restaurantOrder->id,
            ]);

          // Send WhatsApp message to user
          // (new Dialog360())->sendWhatsAppMessage(
          //   $user->mobile_number,
          //   "Your order payment of {$amount} CZK has been successfully processed.\nThe restaurant will contact you soon to confirm the details. "
          // );

          $restaurant = Restaurant::find($restaurantId);
          $restaurant_name = $restaurant->user->name;

          // $user = \App\Models\User::find($userId)->first();
          info('user info '.json_encode($user,JSON_PRETTY_PRINT));

          $restaurant_order_json = [
              'mobile_number' => $user->mobile_number,
              'amount' => $amount,
              'orderId' => $restaurantOrder->id,
              'restaurant_name' => $restaurant_name,
              'restaurant_mobile_number' => $restaurant->mobile_number,
              'platform_name' => 'CITIO'
          ];
          // info($announcement_json);
          // dispatch(new \App\Jobs\Send360TemplateMessageJob($user,'restaurant_order_payment', $restaurant_order_json, [
          //     'order_id' => $restaurantOrder->id
          // ]));
          (new Dialog360())->sendTemplateWhatsAppMessage($user->mobile_number,'restaurant_order_payment',$user->language_code,$restaurant_order_json);

        } else {
          // Handle failed payment
          (new Dialog360())->sendWhatsAppMessage(
            $user->mobile_number,
            "Your order payment has failed. Please try again or contact support."
          );
        }
      }
    }
  }
   public function handleSchoolAdmission($session){
        $metadata = $session['metadata'] ?? [];
        $userId = $metadata['user_id'] ?? null;
        $schoolId = $metadata['school_id'] ?? null;
        $admissionId = $metadata['admission_id'] ?? null;

        if ($userId && $schoolId && $admissionId) {

            $schoolAdmission = SchoolAdmission::query()
              ->where('id', $admissionId)
              ->where('school_id', $schoolId)
              ->where('user_id', $userId)
              ->where('payment_status', 'pending') // Ensure we only update pending admissions
              ->first();

            if ($schoolAdmission ) {
                $user = $schoolAdmission->user;
                $school = $schoolAdmission->school;
                $schoolAdmission->update([
                  'status' => SchoolAdmissionStatus::INPROCESS->value,
                  'payment_status' => $session['payment_status'],
                ]);
                if ($session['payment_status'] == config('constant.stripe_checkout_status.paid'))
                {
                    // Update the admission payment status
                    // $amount = $session['amount_total'] / 100; // Convert cents to dollars
                    // $admission = $schoolAdmission->payments()->create([
                    //     'amount' => $amount,
                    //     'currency' => $session['currency'],
                    //     'status' => $session['payment_status'] ?? 'paid',
                    //     'type' => 'school_admission',
                    //     'transaction_id' => $session['id'],
                    //     'payment_method' => $session['payment_method_types'][0],
                    //     'purpose' => 'school admission payment'
                    // ]);

                    // $school_admission_payment_json = [
                    //     'user_name' => $user->name,
                    //     'amount' => $amount,
                    //     'currency' => $session['currency'],
                    //     'school' => $school->user->name,
                    //     'reference_id' => $admission->id,
                    //     'mobile_number' => $school->mobile_number,
                    //     'email' => $school->user->email
                    // ];

                    // (new Dialog360())->sendTemplateWhatsAppMessage($user->mobile_number,'school_admission_payment',$user->language_code,$school_admission_payment_json);

                    $amount = $session['amount_total'] / 100;
                     // ✅ Store Payment including payment_intent
                    $payment = $schoolAdmission->payments()->create([
                        'amount' => $amount,
                        'currency' => $session['currency'],
                        'status' => 'paid',
                        'type' => 'school_admission',
                        'transaction_id' => $session['id'], // checkout session id
                        'stripe_payment_intent' => $session['payment_intent'], // 👈 IMPORTANT
                        'payment_method' => $session['payment_method_types'][0] ?? 'card',
                        'purpose' => 'school admission payment',
                    ]);

                    // ✅ Optional notification
                    $school_admission_payment_json = [
                        'user_name' => $user->name,
                        'amount' => $amount,
                        'currency' => $session['currency'],
                        'school' => $school->user->name,
                        'reference_id' => $payment->id,
                        'mobile_number' => $school->mobile_number,
                        'email' => $school->user->email
                    ];

                    (new Dialog360())->sendTemplateWhatsAppMessage(
                        $user->mobile_number,
                        'school_admission_payment',
                        $user->language_code,
                        $school_admission_payment_json
                    );
                }
                elseif ($session['payment_status'] == config('stripe_checkout_status.unpaid') || $session['payment_status'] == "requires_payment_method") {
                    $schoolAdmission->update([
                      'payment_status' => 'failed',
                    ]);

                    $message = "Hi {$user->name}, \nYour admission payment for {$school->user->name} School has failed. Please try again or contact support.";
                    (new Dialog360())->sendWhatsAppMessage($user->mobile_number, $message);
                }
                //        else {
                //          $message = "Dear {$user->name}, your admission payment for {$school->user->name} School is pending. Please complete the payment to finalize your admission.";
                //          (new Dialog360())->sendWhatsAppMessage('918401777870', $message);
                //        }
            }
        }
    }

    //implemenr in payments module
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $data = $payload['data']['object'];
            info('subscription data',$data);
            $payments = [];

            if (! $user->subscriptions->contains('stripe_id', $data['id'])) {
                if (isset($data['trial_end'])) {
                    $trialEndsAt = Carbon::createFromTimestamp($data['trial_end']);
                } else {
                    $trialEndsAt = null;
                }

                $firstItem = $data['items']['data'][0];
                $isSinglePrice = count($data['items']['data']) === 1;
                $metadata = $data['metadata'];
                info('subscription payment meta data', $metadata);
                $payments['currency'] = 'czk';
                $payments['payment_method'] = 'card';
                $payments['type'] = 'business_register';
                $payments['transaction_id'] = $data['id'];
                $payments['purpose'] = 'business_subscription';
                $payments['paymentable_id'] = $metadata['business_id'];
                $payments['paymentable_type'] = $metadata['business_type'];

                $subscription = $user->subscriptions()->create([
                    'type' => $data['metadata']['type'] ?? $data['metadata']['name'] ?? $this->newSubscriptionType($payload),
                    'stripe_id' => $data['id'],
                    'stripe_status' => $data['status'],
                    'stripe_price' => $isSinglePrice ? $firstItem['price']['id'] : null,
                    'quantity' => $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null,
                    'trial_ends_at' => $trialEndsAt,
                    'ends_at' => null,
                ]);

                foreach ($data['items']['data'] as $item) {
                    $payments['amount'] = $item['plan']['amount'] / 100;
                    $subscription->items()->create([
                        'stripe_id' => $item['id'],
                        'stripe_product' => $item['price']['product'],
                        'stripe_price' => $item['price']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]);
                }
                info('payments data are ');
                info(json_encode($payments));
                Payment::create($payments);
            }

            // Terminate the billable's generic trial if it exists...
            if (! is_null($user->trial_ends_at)) {
                $user->trial_ends_at = null;
                $user->save();
            }
        }
        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            // info($user);
            $data = $payload['data']['object'];

            $subscription = $user->subscriptions()->firstOrNew(['stripe_id' => $data['id']]);

            if (
                isset($data['status']) &&
                $data['status'] === StripeSubscription::STATUS_INCOMPLETE_EXPIRED
            ) {
                $subscription->items()->delete();
                $subscription->delete();

                return;
            }

            $subscription->type = $subscription->type ?? $data['metadata']['type'] ?? $data['metadata']['name'] ?? $this->newSubscriptionType($payload);

            $firstItem = $data['items']['data'][0];
            $isSinglePrice = count($data['items']['data']) === 1;

            // Price...
            $subscription->stripe_price = $isSinglePrice ? $firstItem['price']['id'] : null;

            // Quantity...
            $subscription->quantity = $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null;

            // Trial ending date...
            if (isset($data['trial_end'])) {
                $trialEnd = Carbon::createFromTimestamp($data['trial_end']);

                if (! $subscription->trial_ends_at || $subscription->trial_ends_at->ne($trialEnd)) {
                  $subscription->trial_ends_at = $trialEnd;
                }
            }

            // Cancellation date...
            if ($data['cancel_at_period_end'] ?? false) {
                $subscription->ends_at = $subscription->onTrial()
                    ? $subscription->trial_ends_at
                    : Carbon::createFromTimestamp($data['current_period_end']);
                $user->subscription_status = 'canceled';
            } elseif (isset($data['cancel_at']) || isset($data['canceled_at'])) {
                $subscription->ends_at = Carbon::createFromTimestamp($data['cancel_at'] ?? $data['canceled_at']);
                $user->subscription_status = 'canceled';
            } else {
                $subscription->ends_at = null;
            }

            // Status...
            if (isset($data['status'])) {
                $subscription->stripe_status = $data['status'];
            }

            $subscription->save();
            $user->save();

            // Update subscription items...
            if (isset($data['items'])) {
                $subscriptionItemIds = [];

                foreach ($data['items']['data'] as $item) {
                    $subscriptionItemIds[] = $item['id'];

                    $subscription->items()->updateOrCreate([
                        'stripe_id' => $item['id'],
                    ], [
                        'stripe_product' => $item['price']['product'],
                        'stripe_price' => $item['price']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]);
                }

                // Delete items that aren't attached to the subscription anymore...
                $subscription->items()->whereNotIn('stripe_id', $subscriptionItemIds)->delete();
            }
        }
        return $this->successMethod();
    }
}
