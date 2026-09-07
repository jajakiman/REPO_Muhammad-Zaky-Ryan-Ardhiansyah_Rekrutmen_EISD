<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', ['campuses' => Campus::active()->orderBy('name')->get()]);
    }

    public function store(RegisterRequest $request): View
    {
        $user = User::create($request->safe()->merge([
            'role' => 'reporter',
            'is_active' => true,
            'campus_area_id' => null,
        ])->all());

        Auth::login($user);
        $request->session()->regenerate();

        return view('auth.success', [
            'title' => 'Registrasi Berhasil',
            'message' => 'Registrasi berhasil. Selamat datang di AksesLoka.',
            'redirectUrl' => route('reporter.dashboard'),
        ]);
    }
}
