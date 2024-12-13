<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}
