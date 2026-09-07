<?php

namespace App\Http\Controllers;

use App\Models\AccessibilityReport;
use App\Models\Campus;
use App\Models\CampusLocation;
use App\Models\LocationAccessibilityFeature;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'totalCampuses' => Campus::active()->count(),
            'totalLocations' => CampusLocation::active()->whereHas('campusArea', function ($q) {
                $q->active()->whereHas('campus', fn ($c) => $c->active());
            })->count(),
            'totalFacilities' => LocationAccessibilityFeature::whereHas('accessibilityFeature', fn ($q) => $q->active())
                ->whereHas('campusLocation', function ($q) {
                    $q->active()->whereHas('campusArea', function ($a) {
                        $a->active()->whereHas('campus', fn ($c) => $c->active());
                    });
                })->count(),
            'totalResolvedReports' => AccessibilityReport::where('status', 'resolved')->count(),
        ];

        return view('home', compact('stats'));
    }
}
