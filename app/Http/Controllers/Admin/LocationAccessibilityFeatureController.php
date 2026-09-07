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
        $hierarchyIsActive = $this->hierarchyIsActive($location);
        $hasEligibleFeature = $hierarchyIsActive && $this->eligibleFeatures($location)->exists();

        return view('admin.location-features.index', [
            'location' => $location,
            'assignments' => $location->locationAccessibilityFeatures()->with('accessibilityFeature')->orderBy('id')->get(),
            'canAssign' => $hasEligibleFeature,
            'assignmentUnavailableMessage' => $hierarchyIsActive
                ? 'Semua fasilitas aktif sudah terpasang pada lokasi ini.'
                : 'Kampus, area, dan lokasi harus aktif untuk memasang fasilitas.',
        ]);
    }

    public function create(CampusLocation $location): View|RedirectResponse
    {
        if (! $this->hierarchyIsActive($location)) {
            return $this->redirect($location, 'Kampus, area, dan lokasi harus aktif untuk memasang fasilitas.', 'error');
        }

        $features = $this->eligibleFeatures($location)->get();
        if ($features->isEmpty()) {
            return $this->redirect($location, 'Semua fasilitas aktif sudah terpasang pada lokasi ini.', 'error');
        }

        return view('admin.location-features.create', [
            'location' => $location,
            'features' => $features,
        ]);
    }

    public function store(StoreLocationAccessibilityFeatureRequest $request, CampusLocation $location): RedirectResponse
    {
        $location->locationAccessibilityFeatures()->create($request->safe()->except('location'));

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

    private function hierarchyIsActive(CampusLocation $location): bool
    {
        return $location->is_active && $location->campusArea->is_active && $location->campusArea->campus->is_active;
    }

    private function eligibleFeatures(CampusLocation $location)
    {
        return AccessibilityFeature::active()
            ->whereNotIn('id', $location->locationAccessibilityFeatures()->select('accessibility_feature_id'))
            ->orderBy('name');
    }

    private function redirect(CampusLocation $location, string $message, string $type = 'success'): RedirectResponse
    {
        return redirect()->route('admin.locations.features.index', $location)->with($type, $message);
    }
}
