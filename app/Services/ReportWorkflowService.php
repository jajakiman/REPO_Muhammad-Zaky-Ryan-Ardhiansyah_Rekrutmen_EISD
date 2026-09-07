<?php

namespace App\Services;

use App\Models\AccessibilityReport;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class ReportWorkflowService
{
    /**
     * Verify and claim a submitted report for the officer.
     */
    public function verify(AccessibilityReport $report, User $officer, string $priority): void
    {
        DB::transaction(function () use ($report, $officer, $priority) {
            /** @var AccessibilityReport $locked */
            $locked = AccessibilityReport::where('id', $report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'submitted' || $locked->officer_id !== null) {
                throw new DomainException('Laporan telah diproses oleh Petugas lain.');
            }

            $reportAreaId = $locked->locationAccessibilityFeature
                ?->campusLocation
                ?->campus_area_id;

            if ($reportAreaId !== $officer->campus_area_id) {
                abort(403, 'Laporan berada di luar area tugas Anda.');
            }

            $locked->update([
                'officer_id' => $officer->id,
                'priority' => $priority,
                'status' => 'verified',
                'verified_at' => now(),
            ]);
        });
    }

    /**
     * Reject a submitted report with a mandatory reason.
     */
    public function reject(AccessibilityReport $report, User $officer, string $reason): void
    {
        DB::transaction(function () use ($report, $officer, $reason) {
            /** @var AccessibilityReport $locked */
            $locked = AccessibilityReport::where('id', $report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'submitted' || $locked->officer_id !== null) {
                throw new DomainException('Laporan telah diproses oleh Petugas lain.');
            }

            $reportAreaId = $locked->locationAccessibilityFeature
                ?->campusLocation
                ?->campus_area_id;

            if ($reportAreaId !== $officer->campus_area_id) {
                abort(403, 'Laporan berada di luar area tugas Anda.');
            }

            $locked->update([
                'officer_id' => $officer->id,
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_at' => now(),
            ]);
        });
    }

    /**
     * Start handling a verified report.
     */
    public function start(AccessibilityReport $report, User $officer): void
    {
        DB::transaction(function () use ($report, $officer) {
            /** @var AccessibilityReport $locked */
            $locked = AccessibilityReport::where('id', $report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'verified') {
                throw new DomainException('Hanya laporan berstatus terverifikasi yang dapat dimulai penanganannya.');
            }

            if ($locked->officer_id !== $officer->id) {
                throw new DomainException('Hanya petugas penanggung jawab yang dapat memulai penanganan laporan ini.');
            }

            $locked->update([
                'status' => 'in_progress',
                'handling_started_at' => now(),
            ]);
        });
    }

    /**
     * Resolve an in-progress report and atomically update the facility condition.
     */
    public function resolve(AccessibilityReport $report, User $officer, array $data, ?string $photoPath = null): void
    {
        DB::transaction(function () use ($report, $officer, $data, $photoPath) {
            /** @var AccessibilityReport $locked */
            $locked = AccessibilityReport::where('id', $report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'in_progress') {
                throw new DomainException('Hanya laporan yang sedang dalam penanganan yang dapat diselesaikan.');
            }

            if ($locked->officer_id !== $officer->id) {
                throw new DomainException('Hanya petugas penanggung jawab yang dapat menyelesaikan laporan ini.');
            }

            $locked->update([
                'status' => 'resolved',
                'resolution_notes' => $data['resolution_notes'],
                'resolution_photo_path' => $photoPath,
                'resolved_at' => now(),
            ]);

            $facility = $locked->locationAccessibilityFeature;
            if ($facility) {
                $facility->update([
                    'condition' => $data['condition'],
                    'last_checked_at' => now(),
                ]);
            }
        });
    }
}
