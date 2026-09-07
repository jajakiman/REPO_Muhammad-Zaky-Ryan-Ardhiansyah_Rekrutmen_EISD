<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', ['campuses' => Campus::active()->orderBy('name')->get()]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        User::create($request->safe()->merge([
            'role' => 'reporter',
            'is_active' => true,
            'campus_area_id' => null,
        ])->all());

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan masuk ke AksesLoka.');
    }
}
