<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Officer\RejectReportRequest;
use App\Http\Requests\Officer\ResolveReportRequest;
use App\Http\Requests\Officer\VerifyReportRequest;
use App\Models\AccessibilityReport;
use App\Services\ReportWorkflowService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
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

    public function start(AccessibilityReport $report, ReportWorkflowService $service): RedirectResponse
    {
        $this->ensureAreaAccess($report);

        try {
            $service->start($report, auth()->user());

            return redirect()->route('officer.reports.show', $report)
                ->with('success', 'Penanganan laporan telah dimulai.');
        } catch (DomainException $e) {
            return redirect()->route('officer.reports.show', $report)
                ->with('error', $e->getMessage());
        }
    }

    public function resolve(ResolveReportRequest $request, AccessibilityReport $report, ReportWorkflowService $service): RedirectResponse
    {
        $this->ensureAreaAccess($report);

        $photoPath = null;
        if ($request->hasFile('resolution_photo')) {
            $photoPath = $request->file('resolution_photo')->store('resolutions', 'report-photos');
            if ($photoPath === false) {
                return redirect()->route('officer.reports.show', $report)
                    ->withInput($request->safe()->except('resolution_photo'))
                    ->with('error', 'Foto hasil penanganan gagal disimpan. Silakan coba kembali.');
            }
        }

        try {
            $service->resolve($report, auth()->user(), $request->validated(), $photoPath);

            return redirect()->route('officer.reports.show', $report)
                ->with('success', 'Laporan berhasil diselesaikan dan kondisi fasilitas diperbarui.');
        } catch (DomainException $e) {
            if ($photoPath && Storage::disk('report-photos')->exists($photoPath)) {
                Storage::disk('report-photos')->delete($photoPath);
            }

            return redirect()->route('officer.reports.show', $report)
                ->with('error', $e->getMessage());
        }
    }

    public function history(Request $request): View
    {
        $officer = auth()->user();
        abort_unless($officer->campus_area_id, 403, 'Anda belum ditugaskan pada area kampus manapun.');

        $reports = AccessibilityReport::whereHas('locationAccessibilityFeature.campusLocation', function ($q) use ($officer) {
            $q->where('campus_area_id', $officer->campus_area_id);
        })->whereIn('status', ['resolved', 'rejected', 'cancelled'])
            ->with([
                'locationAccessibilityFeature.campusLocation.campusArea.campus',
                'locationAccessibilityFeature.accessibilityFeature',
                'issueCategory',
                'reporter',
                'officer',
            ])
            ->latest()
            ->get();

        return view('officer.history.index', [
            'reports' => $reports,
            'area' => $officer->campusArea,
        ]);
    }

    private function ensureAreaAccess(AccessibilityReport $report): void
    {
        $reportAreaId = $report->locationAccessibilityFeature
            ?->campusLocation
            ?->campus_area_id;

        abort_unless($reportAreaId === auth()->user()->campus_area_id, 403, 'Laporan ini berada di luar area tugas Anda.');
    }
}
