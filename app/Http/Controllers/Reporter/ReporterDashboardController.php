<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityReport;
use Illuminate\View\View;

class ReporterDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $totalReports = AccessibilityReport::where('reporter_id', $user->id)->count();
        $activeReports = AccessibilityReport::where('reporter_id', $user->id)
            ->whereIn('status', ['submitted', 'verified', 'in_progress'])
            ->count();
        $resolvedReports = AccessibilityReport::where('reporter_id', $user->id)
            ->where('status', 'resolved')
            ->count();
        $recentReports = AccessibilityReport::where('reporter_id', $user->id)
            ->with([
                'locationAccessibilityFeature.campusLocation.campusArea.campus',
                'locationAccessibilityFeature.accessibilityFeature',
                'issueCategory',
            ])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboards.reporter', compact(
            'totalReports',
            'activeReports',
            'resolvedReports',
            'recentReports'
        ));
    }
}
