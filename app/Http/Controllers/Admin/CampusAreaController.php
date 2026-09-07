<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampusAreaRequest;
use App\Http\Requests\Admin\UpdateCampusAreaRequest;
use App\Models\Campus;
use App\Models\CampusArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampusAreaController extends Controller
{
    public function index(Campus $campus): View
    {
        return view('admin.areas.index', ['campus' => $campus, 'areas' => $campus->areas()->orderBy('name')->get()]);
    }

    public function create(Campus $campus): View
    {
        return view('admin.areas.create', compact('campus'));
    }

    public function store(StoreCampusAreaRequest $request, Campus $campus): RedirectResponse
    {
        $campus->areas()->create($request->safe()->only('name'));

        return redirect()->route('admin.campuses.areas.index', $campus)
            ->with('success', 'Area kampus berhasil ditambahkan.')
            ->with('success_modal', true);
    }

    public function edit(Campus $campus, CampusArea $area): View
    {
        $this->ensureContext($campus, $area);

        return view('admin.areas.edit', compact('campus', 'area'));
    }

    public function update(UpdateCampusAreaRequest $request, Campus $campus, CampusArea $area): RedirectResponse
    {
        $this->ensureContext($campus, $area);
        $area->update($request->validated());

        return redirect()->route('admin.campuses.areas.index', $campus)->with('success', 'Area kampus berhasil disimpan.');
    }

    public function deactivate(Campus $campus, CampusArea $area): RedirectResponse
    {
        $this->ensureContext($campus, $area);
        $area->update(['is_active' => false]);

        return redirect()->route('admin.campuses.areas.index', $campus)->with('success', 'Area kampus berhasil dinonaktifkan.');
    }

    public function status(Request $request, Campus $campus, CampusArea $area): RedirectResponse
    {
        $this->ensureContext($campus, $area);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        if ($data['is_active'] && ! $campus->is_active) {
            return back()->withErrors(['is_active' => 'Aktifkan kampus terlebih dahulu.']);
        }
        $area->update($data);

        return redirect()->route('admin.campuses.areas.index', $campus)->with('success', 'Status area berhasil diperbarui.');
    }

    private function ensureContext(Campus $campus, CampusArea $area): void
    {
        abort_unless($area->campus_id === $campus->id, 404);
    }
}
