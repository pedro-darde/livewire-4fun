<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login()
    {
        return Inertia::render('Auth/Login');
    }

    public function register()
    {
        return Inertia::render('Auth/Register');
    }

    public function saveRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        $user = new User();
        $user->fill($validated);
        $user->save();

        if (Auth::attempt(['email' => $user->email, 'password' => $user->password])) {
            $request->session()->regenerate();

            return response()->json(['message' => 'Usuário cadastrado com sucesso!']);
        }

        return redirect()->route('login');


    }

    public function doLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $rememberMe = $request->input('remember_me');

        if (Auth::attempt($credentials, $rememberMe)) {
            $request->session()->regenerate();
            return response()->json(['message' => 'Usuário logado com sucesso!']);
        }

        return response()->json([
            'message' => 'Usuário ou senha inválidos!'
        ], 422);
    }
}
