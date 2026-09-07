<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampusLocationRequest;
use App\Http\Requests\Admin\UpdateCampusLocationRequest;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampusLocationController extends Controller
{
    public function index(Campus $campus, CampusArea $area): View
    {
        $this->ensureAreaContext($campus, $area);

        return view('admin.locations.index', ['campus' => $campus, 'area' => $area, 'locations' => $area->locations()->orderBy('name')->get()]);
    }

    public function create(Campus $campus, CampusArea $area): View
    {
        $this->ensureAreaContext($campus, $area);

        return view('admin.locations.create', compact('campus', 'area'));
    }

    public function store(StoreCampusLocationRequest $request, Campus $campus, CampusArea $area): RedirectResponse
    {
        $this->ensureAreaContext($campus, $area);
        $area->locations()->create($request->safe()->except('area'));

        return $this->redirect($campus, $area, 'Lokasi kampus berhasil disimpan.');
    }

    public function edit(Campus $campus, CampusArea $area, CampusLocation $location): View
    {
        $this->ensureLocationContext($campus, $area, $location);

        return view('admin.locations.edit', compact('campus', 'area', 'location'));
    }

    public function update(UpdateCampusLocationRequest $request, Campus $campus, CampusArea $area, CampusLocation $location): RedirectResponse
    {
        $this->ensureLocationContext($campus, $area, $location);
        $location->update($request->validated());

        return $this->redirect($campus, $area, 'Lokasi kampus berhasil disimpan.');
    }

    public function deactivate(Campus $campus, CampusArea $area, CampusLocation $location): RedirectResponse
    {
        $this->ensureLocationContext($campus, $area, $location);
        $location->update(['is_active' => false]);

        return $this->redirect($campus, $area, 'Lokasi kampus berhasil dinonaktifkan.');
    }

    private function ensureAreaContext(Campus $campus, CampusArea $area): void
    {
        abort_unless($area->campus_id === $campus->id, 404);
    }

    private function ensureLocationContext(Campus $campus, CampusArea $area, CampusLocation $location): void
    {
        $this->ensureAreaContext($campus, $area);
        abort_unless($location->campus_area_id === $area->id, 404);
    }

    private function redirect(Campus $campus, CampusArea $area, string $message): RedirectResponse
    {
        return redirect()->route('admin.campuses.areas.locations.index', [$campus, $area])->with('success', $message);
    }
}
