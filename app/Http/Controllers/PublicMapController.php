<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\CampusLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicMapController extends Controller
{
    public function index(Request $request): View
    {
        $campuses = Campus::active()->orderBy('name')->get();

        $hasAnyLocations = CampusLocation::active()
            ->whereHas('campusArea', function ($q) {
                $q->active()->whereHas('campus', fn ($c) => $c->active());
            })->exists();

        $selectedCampusId = null;
        if ($request->filled('campus_id')) {
            $selectedCampusId = $request->input('campus_id');
        } elseif (! $request->filled('q') && auth()->check() && auth()->user()->campus_id) {
            $selectedCampusId = auth()->user()->campus_id;
        }

        $selectedCampus = $selectedCampusId ? Campus::find($selectedCampusId) : null;

        $query = CampusLocation::active()
            ->whereHas('campusArea', function ($q) {
                $q->active()->whereHas('campus', fn ($c) => $c->active());
            })
            ->with(['campusArea.campus']);

        if ($selectedCampusId) {
            $query->whereHas('campusArea', fn ($q) => $q->where('campus_id', $selectedCampusId));
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->input('q').'%');
        }

        if ($request->filled('location_type')) {
            $query->where('location_type', $request->input('location_type'));
        }

        if ($request->filled('accessibility_status')) {
            $query->where('accessibility_status', $request->input('accessibility_status'));
        }

        $locations = $query->orderBy('name')->get();

        $locationTypes = [
            'building' => 'Gedung',
            'library' => 'Perpustakaan',
            'worship_place' => 'Tempat Ibadah',
            'green_space' => 'Ruang Terbuka Hijau',
            'parking' => 'Area Parkir',
            'pedestrian_area' => 'Jalur Pejalan Kaki',
            'shuttle_stop' => 'Halte / Titik Kumpul',
        ];

        $accessibilityStatuses = [
            'accessible' => 'Aksesibel Penuh',
            'partially_accessible' => 'Aksesibel Sebagian',
            'inaccessible' => 'Belum Aksesibel',
            'not_assessed' => 'Belum Dinilai',
        ];

        $mapMarkers = $locations->map(fn ($loc) => [
            'id' => $loc->id,
            'name' => $loc->name,
            'lat' => (float) $loc->latitude,
            'lng' => (float) $loc->longitude,
            'status' => $loc->accessibility_status,
            'status_label' => $accessibilityStatuses[$loc->accessibility_status] ?? $loc->accessibility_status,
            'type_label' => $locationTypes[$loc->location_type] ?? $loc->location_type,
            'campus_name' => $loc->campusArea->campus->name,
            'area_name' => $loc->campusArea->name,
            'url' => route('locations.show', $loc),
        ]);

        $hasActiveFilter = $request->filled('q') || $request->filled('location_type') || $request->filled('accessibility_status') || ($request->filled('campus_id') && (!auth()->check() || !auth()->user()->campus_id || (string)auth()->user()->campus_id !== (string)$request->input('campus_id')));

        return view('map.index', compact(
            'locations',
            'campuses',
            'locationTypes',
            'accessibilityStatuses',
            'mapMarkers',
            'hasAnyLocations',
            'selectedCampus',
            'hasActiveFilter'
        ));
    }

    public function show(CampusLocation $location): View
    {
        abort_unless(
            $location->is_active && $location->campusArea?->is_active && $location->campusArea?->campus?->is_active,
            404
        );

        $location->load([
            'campusArea.campus',
            'locationAccessibilityFeatures.accessibilityFeature',
        ]);

        return view('map.show', compact('location'));
    }
}
