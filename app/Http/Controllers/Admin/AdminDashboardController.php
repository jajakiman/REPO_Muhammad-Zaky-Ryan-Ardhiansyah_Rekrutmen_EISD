<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityFeature;
use App\Models\AccessibilityReport;
use App\Models\Campus;
use App\Models\CampusLocation;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalCampuses = Campus::active()->count();
        $totalLocations = CampusLocation::active()->count();
        $totalFacilities = AccessibilityFeature::active()->count();
        $totalOfficers = User::where('role', 'officer')->where('is_active', true)->count();

        $activeReports = AccessibilityReport::whereIn('status', ['submitted', 'verified', 'in_progress'])->count();
        $resolvedReports = AccessibilityReport::where('status', 'resolved')->count();

        $recentReports = AccessibilityReport::with([
            'locationAccessibilityFeature.campusLocation.campusArea.campus',
            'locationAccessibilityFeature.accessibilityFeature',
            'issueCategory',
            'reporter',
            'officer',
        ])->latest()->take(5)->get();

        return view('dashboards.admin', compact(
            'totalCampuses',
            'totalLocations',
            'totalFacilities',
            'totalOfficers',
            'activeReports',
            'resolvedReports',
            'recentReports'
        ));
    }
}
