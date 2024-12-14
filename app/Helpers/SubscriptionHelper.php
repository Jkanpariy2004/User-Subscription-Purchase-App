<?php

namespace App\Helpers;

class SubscriptionHelper
{
    public static function start_monthly_trial_subscription($customer_id, $user_id, $subscriptionPlan)
    {
        try {
            $stripeData = null;

            return 'return';
        } catch (\Exception $e) {
            return null;
        }
    }
}
