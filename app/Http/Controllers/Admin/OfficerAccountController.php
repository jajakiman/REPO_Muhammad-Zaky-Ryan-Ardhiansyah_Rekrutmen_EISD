<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOfficerRequest;
use App\Http\Requests\Admin\UpdateOfficerRequest;
use App\Models\CampusArea;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfficerAccountController extends Controller
{
    public function index(): View
    {
        $officers = User::where('role', 'officer')
            ->with(['campusArea.campus', 'campus'])
            ->orderBy('name')
            ->get();

        return view('admin.officers.index', compact('officers'));
    }

    public function create(): View
    {
        $areas = CampusArea::active()
            ->whereHas('campus', fn ($q) => $q->active())
            ->with('campus')
            ->orderBy('name')
            ->get();

        return view('admin.officers.create', compact('areas'));
    }

    public function store(StoreOfficerRequest $request): RedirectResponse
    {
        $area = CampusArea::findOrFail($request->validated('campus_area_id'));

        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => 'officer',
            'campus_id' => $area->campus_id,
            'campus_area_id' => $area->id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.officers.index')
            ->with('success', 'Akun petugas berhasil dibuat.')
            ->with('success_modal', true);
    }

    public function edit(User $officer): View
    {
        abort_unless($officer->role === 'officer', 404);

        $areas = CampusArea::active()
            ->whereHas('campus', fn ($q) => $q->active())
            ->with('campus')
            ->orderBy('name')
            ->get();

        return view('admin.officers.edit', compact('officer', 'areas'));
    }

    public function update(UpdateOfficerRequest $request, User $officer): RedirectResponse
    {
        abort_unless($officer->role === 'officer', 404);

        $area = CampusArea::findOrFail($request->validated('campus_area_id'));

        $officer->update([
            'name' => $request->validated('name'),
            'campus_id' => $area->campus_id,
            'campus_area_id' => $area->id,
            'is_active' => (bool) $request->validated('is_active'),
        ]);

        return redirect()->route('admin.officers.index')
            ->with('success', 'Akun petugas berhasil diperbarui.');
    }

    public function deactivate(User $officer): RedirectResponse
    {
        abort_unless($officer->role === 'officer', 404);

        $officer->update(['is_active' => false]);

        return redirect()->route('admin.officers.index')
            ->with('success', 'Akun petugas berhasil dinonaktifkan.');
    }
}
