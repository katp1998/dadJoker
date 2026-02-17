<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Login extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))){
            //create new session
            $request->session()->regenerate();
            return redirect()->intended('/home')->with('success', 'welcome back!');
        }

        return back()
            ->withErrors(['email'=>'The provided credentials do not match our records'])
            ->onlyInput('email');
    }
}
