<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');

        SubscriptionPlan::insert([
            [
                'name' => 'Monthly',
                'stripe_price_id' => 'price_1QVPsZDIQXA66Y828t9GBQIq',
                'trial_days' => 5,
                'amount' => 12,
                'type' => 0,
                'enabled' => 1,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ],
            [
                'name' => 'Yearly',
                'stripe_price_id' => 'price_1QVPuBDIQXA66Y82gZkkmQN2',
                'trial_days' => 5,
                'amount' => 100,
                'type' => 1,
                'enabled' => 1,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ],
            [
                'name' => 'Lifetime',
                'stripe_price_id' => 'price_1QVPz9DIQXA66Y82mrqvd177',
                'trial_days' => 5,
                'amount' => 400,
                'type' => 2,
                'enabled' => 1,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ],
        ]);
    }
}
