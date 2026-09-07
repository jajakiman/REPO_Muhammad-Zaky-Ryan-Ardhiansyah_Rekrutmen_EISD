<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityReport;
use Illuminate\View\View;

class OfficerDashboardController extends Controller
{
    public function __invoke(): View
    {
        $officer = auth()->user();
        $area = $officer->campusArea;

        $submittedCount = 0;
        $inProgressCount = 0;
        $resolvedCount = 0;
        $recentQueue = collect();

        if ($officer->campus_area_id) {
            $submittedCount = AccessibilityReport::whereHas('locationAccessibilityFeature.campusLocation', fn ($q) => $q->where('campus_area_id', $officer->campus_area_id))
                ->where('status', 'submitted')
                ->count();

            $inProgressCount = AccessibilityReport::whereHas('locationAccessibilityFeature.campusLocation', fn ($q) => $q->where('campus_area_id', $officer->campus_area_id))
                ->where('status', 'in_progress')
                ->count();

            $resolvedCount = AccessibilityReport::whereHas('locationAccessibilityFeature.campusLocation', fn ($q) => $q->where('campus_area_id', $officer->campus_area_id))
                ->where('status', 'resolved')
                ->count();

            $recentQueue = AccessibilityReport::whereHas('locationAccessibilityFeature.campusLocation', fn ($q) => $q->where('campus_area_id', $officer->campus_area_id))
                ->where('status', 'submitted')
                ->with([
                    'locationAccessibilityFeature.campusLocation.campusArea.campus',
                    'locationAccessibilityFeature.accessibilityFeature',
                    'issueCategory',
                    'reporter',
                ])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboards.officer', compact(
            'officer',
            'area',
            'submittedCount',
            'inProgressCount',
            'resolvedCount',
            'recentQueue'
        ));
    }
}
