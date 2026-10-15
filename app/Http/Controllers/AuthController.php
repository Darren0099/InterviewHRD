<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function show(Request $request)
    {
        if ((string) config('hrd.auth.password') === '' || $request->session()->get('hrd_auth')) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = (string) config('hrd.auth.username');
        $pass = (string) config('hrd.auth.password');

        if ($pass === '') {
            return back()->withErrors(['username' => 'HRD_PASSWORD belum diatur di server.']);
        }

        // Password di .env boleh teks biasa atau hash bcrypt ($2y$...)
        $passOk = str_starts_with($pass, '$2y$')
            ? Hash::check($data['password'], $pass)
            : hash_equals($pass, $data['password']);
        $userOk = hash_equals(mb_strtolower($user), mb_strtolower($data['username']));

        if (! ($userOk && $passOk)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('hrd_auth', true);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
