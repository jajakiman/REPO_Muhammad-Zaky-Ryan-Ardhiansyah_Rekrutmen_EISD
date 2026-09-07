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
}
