<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            if (Auth::user()->status == 1) {
                return redirect()->route('landingPage.admin');
            } elseif (Auth::user()->status == 2 || Auth::user()->status == 3) {
                return redirect()->route('landingPage.user');
            }
        } else {
            // Authentication failed...
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }
    }

    public function landingPage()
    {
        if (Auth::check()) {
            if (Auth::user()->status == 1) {
                return view('landingPage.admin');
            } elseif (Auth::user()->status == 2 || Auth::user()->status == 3) {
                return view('landingPage.user');
            }
        } else {
            return redirect()->route('login');
        }
    }
}
