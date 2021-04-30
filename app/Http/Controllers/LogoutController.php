<?php

namespace App\Http\Controllers;

class LogoutController extends Controller
{
    public function logout()
    {
        if (auth()->check())
            auth()->logout();
        session()->flush();
        return redirect()->route('dashboard');
    }
}
