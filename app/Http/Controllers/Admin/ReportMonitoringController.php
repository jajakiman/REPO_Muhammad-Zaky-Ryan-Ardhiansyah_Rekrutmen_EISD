<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityReport;
use App\Models\Campus;
use App\Models\CampusArea;
use App\Models\CampusLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $campuses = Campus::orderBy('name')->get();
        $areas = CampusArea::orderBy('name')->get();
        $locations = CampusLocation::orderBy('name')->get();

        $query = AccessibilityReport::with([
            'locationAccessibilityFeature.campusLocation.campusArea.campus',
            'locationAccessibilityFeature.accessibilityFeature',
            'issueCategory',
            'reporter',
            'officer',
        ]);

        if ($request->filled('campus_id')) {
            $query->whereHas('locationAccessibilityFeature.campusLocation.campusArea', function ($q) use ($request) {
                $q->where('campus_id', $request->input('campus_id'));
            });
        }

        if ($request->filled('campus_area_id')) {
            $query->whereHas('locationAccessibilityFeature.campusLocation', function ($q) use ($request) {
                $q->where('campus_area_id', $request->input('campus_area_id'));
            });
        }

        if ($request->filled('campus_location_id')) {
            $query->where('location_accessibility_feature_id', function ($q) use ($request) {
                $q->select('id')
                    ->from('location_accessibility_features')
                    ->where('campus_location_id', $request->input('campus_location_id'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $reports = $query->latest()->get();

        $statusLabels = [
            'submitted' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'in_progress' => 'Sedang Ditangani',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
        ];

        $priorityLabels = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
        ];

        return view('admin.reports.index', compact(
            'reports',
            'campuses',
            'areas',
            'locations',
            'statusLabels',
            'priorityLabels'
        ));
    }

    public function show(AccessibilityReport $report): View
    {
        $report->load([
            'locationAccessibilityFeature.campusLocation.campusArea.campus',
            'locationAccessibilityFeature.accessibilityFeature',
            'issueCategory',
            'reporter',
            'officer',
        ]);

        return view('admin.reports.show', compact('report'));
    }
}
