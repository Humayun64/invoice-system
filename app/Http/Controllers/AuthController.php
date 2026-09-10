<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('user_id')) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'user_id'   => $user->id,
                'user_name' => $user->full_name ?: $user->username,
                'user_role' => $user->role,
            ]);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['login' => 'Invalid username or password.'])->withInput();
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
