<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reporter\UpdateProfileRequest;
use App\Models\Campus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('reporter.profile.edit', [
            'user' => request()->user(),
            'campuses' => Campus::active()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('reporter.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
