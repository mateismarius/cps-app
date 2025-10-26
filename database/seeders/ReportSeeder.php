<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\Timesheet;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Creează reports pentru 50% din timesheets aprobate
        $approvedTimesheets = Timesheet::where('approved', true)->get();

        if ($approvedTimesheets->isEmpty()) {
            $this->command->warn('No approved timesheets found!');
            return;
        }

        $summaries = [
            'Completed electrical installation as per schedule. All tests passed.',
            'HVAC unit installed and commissioned. Client sign-off received.',
            'Plumbing works completed. Minor snag list items remain.',
            'Emergency repair completed successfully. System restored.',
            'Routine maintenance completed. No issues found.',
            'Installation progressing well. On schedule for completion.',
            'Wiring and testing completed for retail unit A.',
            'Fire alarm system tested and certified.',
            'LED lighting installation completed in zones 1-3.',
            'Mechanical works completed. Electrical works to follow.',
        ];

        foreach ($approvedTimesheets as $timesheet) {
            // 50% șanse să existe raport
            if (rand(0, 1) === 1) {
                Report::create([
                    'project_id' => $timesheet->project_id,
                    'timesheet_id' => $timesheet->id,
                    'engineer_id' => $timesheet->engineer_id,
                    'report_date' => $timesheet->date,
                    'summary' => $summaries[array_rand($summaries)],
                    'file_path' => null, // Poți adăuga fișiere mai târziu
                    'mime_type' => null,
                ]);
            }
        }

        $this->command->info('Reports created successfully!');
    }
}
