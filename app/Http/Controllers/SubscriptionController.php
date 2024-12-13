<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionDetails;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
