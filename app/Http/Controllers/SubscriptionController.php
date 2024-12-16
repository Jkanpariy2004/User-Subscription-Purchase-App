<?php

namespace App\Http\Controllers;

use App\Helpers\SubscriptionHelper;
use App\Models\CardDetails;
use App\Models\SubscriptionDetails;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Customer;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function LoadSubscription()
    {
        $plans = SubscriptionPlan::where('enabled', 1)->get();
        return view('subscription', ['plans' => $plans]);
    }

    public function getPlanDetails(Request $request)
    {
        try {
            $planData = SubscriptionPlan::where('id', $request->id)->first();

            $ActivePlan = SubscriptionDetails::where(['user_id' => Auth::user()->id, 'status' => 'active'])->count();
            $msg = '';

            if ($ActivePlan == 0 && ($planData->trial_days != null && $planData->trial_days != '')) {
                $msg = "You Will Get " . $planData->trial_days . " days trial, and after we will charge $ " . $planData->amount . "for" . $planData->name . "Subscription Plan.";
            } else {
                $msg = "We will charge $" . $planData->amount . "for" . $planData->name . "Subscription Plan.";
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
                'data' => $planData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function createSubscription(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $secreteKey = env('STRIPE_SECRETE_KEY');
            Stripe::setApiKey($secreteKey);

            $stripeData = $request->data;

            $customer = $this->createCustomer($stripeData['id']);

            $customer_id = $customer->id;

            $subscriptionPlan = SubscriptionPlan::where('id', $request->plan_id)->first();

            if (!$subscriptionPlan) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Invalid subscription plan selected.',
                ], 400);
            }

            if ($subscriptionPlan->type == 0) {
                $subscriptionData = SubscriptionHelper::start_monthly_trial_subscription($customer_id, $user_id, $subscriptionPlan);
            } elseif ($subscriptionPlan->type == 1) {
                $subscriptionData = SubscriptionHelper::start_yearly_trial_subscription($customer_id, $user_id, $subscriptionPlan);
            } elseif ($subscriptionPlan->type == 2) {
                $subscriptionData = SubscriptionHelper::start_lifetime_trial_subscription($customer_id, $user_id, $subscriptionPlan);
            }

            $this->saveCardDetails($stripeData, $user_id, $customer_id);

            if ($subscriptionData) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Subscription Purchased Successfully!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'Subscription Purchased Failed!',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function createCustomer($token_id)
    {
        $customer = Customer::create([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'source' => $token_id,
        ]);

        return $customer;
    }

    public function saveCardDetails($cardData, $user_id, $customer_id)
    {
        CardDetails::updateOrCreate(
            [
                'user_id' => $user_id,
                'card_no' => $cardData['card']['last4'],
            ],
            [
                'user_id' => $user_id,
                'customer_id' => $customer_id,
                'card_id' => $cardData['card']['id'],
                'name' => $cardData['card']['name'],
                'card_no' => $cardData['card']['last4'],
                'brand' => $cardData['card']['brand'],
                'month' => $cardData['card']['exp_month'],
                'year' => $cardData['card']['exp_year'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
