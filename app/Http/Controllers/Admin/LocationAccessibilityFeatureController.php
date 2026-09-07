<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLocationAccessibilityFeatureRequest;
use App\Http\Requests\Admin\UpdateLocationAccessibilityFeatureRequest;
use App\Models\AccessibilityFeature;
use App\Models\CampusLocation;
use App\Models\LocationAccessibilityFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationAccessibilityFeatureController extends Controller
{
    public function index(CampusLocation $location): View
    {
        return view('admin.location-features.index', [
            'location' => $location,
            'assignments' => $location->locationAccessibilityFeatures()->with('accessibilityFeature')->orderBy('id')->get(),
        ]);
    }

    public function create(CampusLocation $location): View
    {
        return view('admin.location-features.create', [
            'location' => $location,
            'features' => AccessibilityFeature::active()
                ->whereNotIn('id', $location->locationAccessibilityFeatures()->select('accessibility_feature_id'))
                ->orderBy('name')->get(),
        ]);
    }

    public function store(StoreLocationAccessibilityFeatureRequest $request, CampusLocation $location): RedirectResponse
    {
        $location->locationAccessibilityFeatures()->create($request->validated());

        return $this->redirect($location, 'Fasilitas berhasil dipasang pada lokasi.');
    }

    public function edit(CampusLocation $location, LocationAccessibilityFeature $locationFeature): View
    {
        $this->ensureContext($location, $locationFeature);

        return view('admin.location-features.edit', compact('location', 'locationFeature'));
    }

    public function update(UpdateLocationAccessibilityFeatureRequest $request, CampusLocation $location, LocationAccessibilityFeature $locationFeature): RedirectResponse
    {
        $this->ensureContext($location, $locationFeature);
        $locationFeature->update($request->validated());

        return $this->redirect($location, 'Detail fasilitas lokasi berhasil diperbarui.');
    }

    private function ensureContext(CampusLocation $location, LocationAccessibilityFeature $locationFeature): void
    {
        abort_unless($locationFeature->campus_location_id === $location->id, 404);
    }

    private function redirect(CampusLocation $location, string $message): RedirectResponse
    {
        return redirect()->route('admin.locations.features.index', $location)->with('success', $message);
    }
}
