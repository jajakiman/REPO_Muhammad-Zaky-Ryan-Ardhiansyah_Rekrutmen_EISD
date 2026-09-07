<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Officer\RejectReportRequest;
use App\Http\Requests\Officer\VerifyReportRequest;
use App\Models\AccessibilityReport;
use App\Services\ReportWorkflowService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficerReportQueueController extends Controller
{
    public function index(Request $request): View
    {
        $officer = auth()->user();
        abort_unless($officer->campus_area_id, 403, 'Anda belum ditugaskan pada area kampus manapun.');

        $query = AccessibilityReport::whereHas('locationAccessibilityFeature.campusLocation', function ($q) use ($officer) {
            $q->where('campus_area_id', $officer->campus_area_id);
        })->with([
            'locationAccessibilityFeature.campusLocation.campusArea.campus',
            'locationAccessibilityFeature.accessibilityFeature',
            'issueCategory',
            'reporter',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default queue shows submitted reports needing action
            $query->where('status', 'submitted');
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $reports = $query->latest()->get();

        return view('officer.queue.index', [
            'reports' => $reports,
            'area' => $officer->campusArea,
        ]);
    }

    public function show(AccessibilityReport $report): View
    {
        $this->ensureAreaAccess($report);

        $report->load([
            'locationAccessibilityFeature.campusLocation.campusArea.campus',
            'locationAccessibilityFeature.accessibilityFeature',
            'issueCategory',
            'reporter',
            'officer',
        ]);

        return view('officer.queue.show', compact('report'));
    }

    public function verify(VerifyReportRequest $request, AccessibilityReport $report, ReportWorkflowService $service): RedirectResponse
    {
        $this->ensureAreaAccess($report);

        try {
            $service->verify($report, auth()->user(), $request->validated('priority'));

            return redirect()->route('officer.reports.show', $report)
                ->with('success', 'Laporan berhasil diverifikasi dan menjadi tanggung jawab Anda.');
        } catch (DomainException $e) {
            return redirect()->route('officer.reports.show', $report)
                ->with('error', $e->getMessage());
        }
    }

    public function reject(RejectReportRequest $request, AccessibilityReport $report, ReportWorkflowService $service): RedirectResponse
    {
        $this->ensureAreaAccess($report);

        try {
            $service->reject($report, auth()->user(), $request->validated('rejection_reason'));

            return redirect()->route('officer.reports.show', $report)
                ->with('success', 'Laporan berhasil ditolak.');
        } catch (DomainException $e) {
            return redirect()->route('officer.reports.show', $report)
                ->with('error', $e->getMessage());
        }
    }

    private function ensureAreaAccess(AccessibilityReport $report): void
    {
        $reportAreaId = $report->locationAccessibilityFeature
            ?->campusLocation
            ?->campus_area_id;

        abort_unless($reportAreaId === auth()->user()->campus_area_id, 403, 'Laporan ini berada di luar area tugas Anda.');
    }
}
