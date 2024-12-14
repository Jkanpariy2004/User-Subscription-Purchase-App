<?php

namespace App\Http\Controllers;

use App\Helpers\SubscriptionHelper;
use App\Models\CardDetails;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stripe\Customer;
use Stripe\Stripe;

class AuthController extends Controller
{
    public function loadRegister()
    {
        return view('register');
    }

    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Registration Successfully!');
    }

    public function loadLogin()
    {
        return view('login');
    }

    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|min:6',
        ]);

        $userCre = $request->only('email', 'password');

        if (Auth::attempt($userCre)) {
            return redirect()->route('dashboard');
        }

        return redirect()->back()->with('error', 'Please Enter Valid Email & Password');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function logout()
    {
        try {
            Auth::logout();

            return redirect()->route('login')->with('success', 'Logout Successfully.');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
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

            $customer_id = $request->id;

            $subscriptionPlan = SubscriptionPlan::where('id', $request->plan_id)->first();

            if ($subscriptionPlan->type == 0) {
                $subscriptionData = SubscriptionHelper::start_monthly_trial_subscription($customer_id, $user_id, $subscriptionPlan);
            } else if ($subscriptionPlan->type == 1) {

            } elseif ($subscriptionPlan->type == 2) {

            }

            $this->saveCardDetails($stripeData, $user_id, $customer_id);

            if ($customer) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Customer Created Successfully!',
                    'Customer' => $customer,
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
