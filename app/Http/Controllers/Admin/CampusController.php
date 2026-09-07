<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampusRequest;
use App\Http\Requests\Admin\UpdateCampusRequest;
use App\Models\Campus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampusController extends Controller
{
    public function index(): View
    {
        return view('admin.campuses.index', ['campuses' => Campus::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.campuses.create');
    }

    public function store(StoreCampusRequest $request): RedirectResponse
    {
        Campus::create($request->validated());

        return redirect()->route('admin.campuses.index')
            ->with('success', 'Data kampus berhasil ditambahkan.')
            ->with('success_modal', true);
    }

    public function edit(Campus $campus): View
    {
        return view('admin.campuses.edit', compact('campus'));
    }

    public function update(UpdateCampusRequest $request, Campus $campus): RedirectResponse
    {
        $campus->update($request->validated());

        return redirect()->route('admin.campuses.index')->with('success', 'Data kampus berhasil disimpan.');
    }

    public function deactivate(Campus $campus): RedirectResponse
    {
        $campus->update(['is_active' => false]);

        return redirect()->route('admin.campuses.index')->with('success', 'Kampus berhasil dinonaktifkan.');
    }
}
