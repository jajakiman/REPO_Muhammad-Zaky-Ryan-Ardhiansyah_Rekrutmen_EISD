<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reporter\StoreReportRequest;
use App\Models\AccessibilityReport;
use App\Models\IssueCategory;
use App\Models\LocationAccessibilityFeature;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = AccessibilityReport::where('reporter_id', auth()->id())
            ->with([
                'locationAccessibilityFeature.campusLocation.campusArea.campus',
                'locationAccessibilityFeature.accessibilityFeature',
                'issueCategory',
                'officer',
            ])
            ->latest()
            ->get();

        return view('reporter.reports.index', compact('reports'));
    }

    public function create(Request $request): View
    {
        $facilityId = $request->query('facility_id');
        abort_unless($facilityId, 404);

        $facility = LocationAccessibilityFeature::with([
            'campusLocation.campusArea.campus',
            'accessibilityFeature',
        ])->findOrFail($facilityId);

        abort_unless(
            $facility->accessibilityFeature?->is_active &&
            $facility->campusLocation?->is_active &&
            $facility->campusLocation?->campusArea?->is_active &&
            $facility->campusLocation?->campusArea?->campus?->is_active,
            404
        );

        $categories = IssueCategory::active()->orderBy('name')->get();

        return view('reporter.reports.create', compact('facility', 'categories'));
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $photoPath = null;

        try {
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('reports', 'report-photos');
            }

            $report = DB::transaction(function () use ($request, $photoPath) {
                $reportCode = $this->generateUniqueReportCode();

                return AccessibilityReport::create([
                    'report_code' => $reportCode,
                    'reporter_id' => auth()->id(),
                    'location_accessibility_feature_id' => $request->validated('location_accessibility_feature_id'),
                    'issue_category_id' => $request->validated('issue_category_id'),
                    'description' => $request->validated('description'),
                    'photo_path' => $photoPath,
                    'status' => 'submitted',
                    'priority' => null,
                    'officer_id' => null,
                ]);
            });

            return redirect()->route('reporter.reports.show', $report)
                ->with('success', 'Laporan masalah berhasil dibuat.');
        } catch (Exception $e) {
            if ($photoPath && Storage::disk('report-photos')->exists($photoPath)) {
                Storage::disk('report-photos')->delete($photoPath);
            }
            throw $e;
        }
    }

    public function show(AccessibilityReport $report): View
    {
        abort_unless($report->reporter_id === auth()->id(), 403);

        $report->load([
            'locationAccessibilityFeature.campusLocation.campusArea.campus',
            'locationAccessibilityFeature.accessibilityFeature',
            'issueCategory',
            'officer',
        ]);

        return view('reporter.reports.show', compact('report'));
    }

    public function cancel(AccessibilityReport $report): RedirectResponse
    {
        abort_unless($report->reporter_id === auth()->id(), 403);

        if ($report->status !== 'submitted' || $report->officer_id !== null) {
            return redirect()->route('reporter.reports.show', $report)
                ->with('error', 'Laporan tidak dapat dibatalkan karena sudah diproses oleh Petugas.');
        }

        $report->update(['status' => 'cancelled']);

        return redirect()->route('reporter.reports.show', $report)
            ->with('success', 'Laporan berhasil dibatalkan.');
    }

    private function generateUniqueReportCode(): string
    {
        $datePrefix = date('Ymd');
        do {
            $code = 'RPT-' . $datePrefix . '-' . strtoupper(Str::random(4));
        } while (AccessibilityReport::where('report_code', $code)->exists());

        return $code;
    }
}
