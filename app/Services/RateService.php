<?php

namespace App\Services;

use App\Models\Rate;
use App\Models\Worker;
use App\Models\Client;
use App\Models\Project;
use App\Models\ClientProjectRate;

class RateService
{
    /**
     * Get applicable rate for a worker on a project
     */
    public function getApplicableRate(Worker $worker, Project $project, string $shiftType = 'day')
    {
        // Priority 1: Client-specific rate for this worker on this project
        $projectRate = ClientProjectRate::where('client_id', $project->client_id)
            ->where('project_id', $project->id)
            ->where('worker_id', $worker->id)
            ->first();

        if ($projectRate) {
            return $projectRate;
        }

        // Priority 2: Client-specific rate for this worker (any project)
        $clientWorkerRate = ClientProjectRate::where('client_id', $project->client_id)
            ->whereNull('project_id')
            ->where('worker_id', $worker->id)
            ->first();

        if ($clientWorkerRate) {
            return $clientWorkerRate;
        }

        // Priority 3: Worker's default rate for this shift type
        $workerRate = Rate::where('worker_id', $worker->id)
            ->where('rate_type', $shiftType)
            ->active()
            ->first();

        if ($workerRate) {
            return $workerRate;
        }

        // Priority 4: Worker's first active rate
        return Rate::where('worker_id', $worker->id)
            ->active()
            ->first();
    }

    /**
     * Create or update worker rate
     */
    public function setWorkerRate(Worker $worker, array $rateData): Rate
    {
        return Rate::updateOrCreate(
            [
                'worker_id' => $worker->id,
                'rate_type' => $rateData['rate_type'],
            ],
            [
                'rateable_type' => Worker::class,
                'rateable_id' => $worker->id,
                'name' => $rateData['name'] ?? "{$worker->full_name} - {$rateData['rate_type']}",
                'rate_amount' => $rateData['rate_amount'],
                'currency' => $rateData['currency'] ?? 'GBP',
                'valid_from' => $rateData['valid_from'] ?? now(),
                'valid_until' => $rateData['valid_until'] ?? null,
                'is_active' => $rateData['is_active'] ?? true,
            ]
        );
    }

    /**
     * Create client-project-worker rate
     */
    public function setClientProjectRate(Client $client, ?Project $project, Worker $worker, array $rateData): ClientProjectRate
    {
        return ClientProjectRate::updateOrCreate(
            [
                'client_id' => $client->id,
                'project_id' => $project?->id,
                'worker_id' => $worker->id,
            ],
            [
                'rate_type' => $rateData['rate_type'],
                'rate_amount' => $rateData['rate_amount'],
                'valid_from' => $rateData['valid_from'] ?? now(),
                'valid_until' => $rateData['valid_until'] ?? null,
            ]
        );
    }

    /**
     * Get all active rates for a worker
     */
    public function getWorkerRates(Worker $worker)
    {
        return Rate::where('worker_id', $worker->id)
            ->active()
            ->get();
    }

    /**
     * Calculate total earnings for worker in period
     */
    public function calculateWorkerEarnings(Worker $worker, $startDate, $endDate)
    {
        return $worker->timesheets()
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get()
            ->sum(fn ($timesheet) => $timesheet->calculateAmount());
    }
}

