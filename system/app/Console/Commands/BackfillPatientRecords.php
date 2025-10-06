<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\PatientRecord;
use Illuminate\Support\Facades\DB;

class BackfillPatientRecords extends Command
{
    protected $signature = 'patient-records:backfill {--force : Recreate records even if they already exist}';
    protected $description = 'Backfill tbl_patient_record from existing appointments (earliest appointment per patient).';

    public function handle(): int
    {
        $this->info('Starting backfill...');

        $force = $this->option('force');

        // Build map of earliest appointment date per patient
        $query = Appointment::query()
            ->whereNotNull('patient_id')
            ->select('patient_id', DB::raw('MIN(appointment_date) as first_date'))
            ->groupBy('patient_id');

        $rows = $query->get();
        $count = 0; $skipped = 0; $updated = 0;

        foreach ($rows as $row) {
            $patientId = $row->patient_id;
            $firstDate = $row->first_date; // date (Y-m-d)

            if (!$force) {
                $existing = PatientRecord::where('patient_id', $patientId)->first();
                if ($existing) { $skipped++; continue; }
            }

            $record = PatientRecord::updateOrCreate(
                ['patient_id' => $patientId],
                ['patient_record_date_registered' => $firstDate . ' 00:00:00']
            );

            if ($record->wasRecentlyCreated) { $count++; } else { $updated++; }
        }

        $this->info("Created: $count | Updated: $updated | Skipped: $skipped");
        $this->info('Done.');
        return Command::SUCCESS;
    }
}
