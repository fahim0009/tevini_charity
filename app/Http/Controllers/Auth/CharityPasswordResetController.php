<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class CharityPasswordResetController extends Controller
{
    public function showLinkRequestForm() {
        return view('auth.charity-forgot-password');
    }

    public function sendResetLinkEmail(Request $request) {
        // dd($request->all());
        $request->validate(['email' => 'required|email']);

        // Use the 'charities' broker we defined in auth.php
        $status = Password::broker('charities')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null) {
        return view('auth.charity-reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        // 1. Validate
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // 2. Explicitly call the 'charities' broker
        $status = Password::broker('charities')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($charity, $password) {
                // $charity here is an instance of App\Models\Charity 
                // because the 'charities' broker points to that model
                $charity->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $charity->save();

                event(new \Illuminate\Auth\Events\PasswordReset($charity));
            }
        );

        // 3. Handle response
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('charity.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
