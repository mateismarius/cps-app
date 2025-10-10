<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Employee;
use App\Models\Subcontractor;
use App\Models\Worker;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record;

        // Load employee data if exists
        if ($user->employee) {
            $data['employee'] = $user->employee->toArray();

            // Load worker data if employee has worker
            if ($user->employee->worker) {
                $worker = $user->employee->worker;
                $data['worker'] = $worker->toArray();
                $data['create_worker'] = true;

                // Load rates - properly formatted for repeater
                $data['worker_rates'] = $worker->rates()
                    ->orderBy('created_at')
                    ->get()
                    ->map(function ($rate) {
                        return [
                            'id' => $rate->id,
                            'name' => $rate->name,
                            'rate_type' => $rate->rate_type,
                            'rate_amount' => $rate->rate_amount,
                            'currency' => $rate->currency,
                            'valid_from' => $rate->valid_from?->format('Y-m-d'),
                            'valid_until' => $rate->valid_until?->format('Y-m-d'),
                            'is_active' => $rate->is_active,
                            'description' => $rate->description,
                        ];
                    })
                    ->toArray();
            }
        }

        // Load subcontractor worker data if exists
        if ($user->subcontractor && $user->subcontractor->workers->isNotEmpty()) {
            $worker = $user->subcontractor->workers->first();
            $data['worker'] = $worker->toArray();
            $data['create_worker'] = true;

            // Load rates - properly formatted for repeater
            $data['worker_rates'] = $worker->rates()
                ->orderBy('created_at')
                ->get()
                ->map(function ($rate) {
                    return [
                        'id' => $rate->id,
                        'name' => $rate->name,
                        'rate_type' => $rate->rate_type,
                        'rate_amount' => $rate->rate_amount,
                        'currency' => $rate->currency,
                        'valid_from' => $rate->valid_from?->format('Y-m-d'),
                        'valid_until' => $rate->valid_until?->format('Y-m-d'),
                        'is_active' => $rate->is_active,
                        'description' => $rate->description,
                    ];
                })
                ->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove nested data that will be handled separately
        unset($data['employee']);
        unset($data['worker']);
        unset($data['worker_rates']);
        unset($data['new_subcontractor']);
        unset($data['create_worker']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            // Get the original form data
            $formData = $this->form->getState();

            // 1. Update the User
            $record->update($data);

            // Update roles
            if (isset($formData['roles'])) {
                $record->syncRoles($formData['roles']);
            }

            // Update permissions
            if (isset($formData['permissions'])) {
                $record->syncPermissions($formData['permissions']);
            }

            // 2. Handle Employee
            if ($record->user_type === 'employee' && isset($formData['employee'])) {
                if ($record->employee) {
                    // Update existing employee
                    $record->employee->update($formData['employee']);
                    $employee = $record->employee;
                } else {
                    // Create new employee
                    $employeeData = $formData['employee'];
                    $employeeData['user_id'] = $record->id;
                    $employee = Employee::create($employeeData);
                }

                // 3. Handle Worker for employee
                if (isset($formData['create_worker']) && $formData['create_worker']) {
                    if ($employee->worker) {
                        // Update existing worker
                        $employee->worker->update($formData['worker']);
                        $worker = $employee->worker;
                    } else {
                        // Create new worker
                        $workerData = $formData['worker'];
                        $workerData['employee_id'] = $employee->id;
                        $workerData['worker_type'] = 'employee';
                        $worker = Worker::create($workerData);
                    }

                    // 4. Handle Rates
                    if (isset($formData['worker_rates']) && is_array($formData['worker_rates'])) {
                        // Delete existing rates not in the new list
                        $newRateIds = collect($formData['worker_rates'])
                            ->filter(fn($rate) => isset($rate['id']) && $rate['id'])
                            ->pluck('id')
                            ->toArray();

                        $worker->rates()
                            ->when(!empty($newRateIds), function ($query) use ($newRateIds) {
                                $query->whereNotIn('id', $newRateIds);
                            })
                            ->delete();

                        // Create or update rates
                        foreach ($formData['worker_rates'] as $rateData) {
                            if (empty($rateData['name']) || empty($rateData['rate_amount'])) {
                                continue; // Skip incomplete rates
                            }

                            if (isset($rateData['id']) && $rateData['id']) {
                                // Update existing rate
                                $worker->rates()->where('id', $rateData['id'])->update($rateData);
                            } else {
                                // Create new rate
                                $rateData['rateable_type'] = Worker::class;
                                $rateData['rateable_id'] = $worker->id;
                                $rateData['worker_id'] = $worker->id;
                                $worker->rates()->create($rateData);
                            }
                        }
                    }
                } elseif ($employee->worker) {
                    // Remove worker if create_worker is false
                    $employee->worker->delete();
                }
            } elseif ($record->employee) {
                // Delete employee if user_type changed
                $record->employee->delete();
            }

            // 5. Handle Subcontractor
            if (in_array($record->user_type, ['subcontractor_ltd', 'self_employed'])) {
                $subcontractor = null;

                // Create new subcontractor if provided
                if (isset($formData['new_subcontractor']) && !empty($formData['new_subcontractor']['name'])) {
                    $subcontractorData = $formData['new_subcontractor'];
                    $subcontractorData['business_type'] = $record->user_type === 'subcontractor_ltd' ? 'ltd' : 'self_employed';

                    $subcontractor = Subcontractor::create($subcontractorData);
                    $record->update(['subcontractor_id' => $subcontractor->id]);
                } else {
                    $subcontractor = $record->subcontractor;
                }

                // Handle Worker for subcontractor
                if (isset($formData['create_worker']) && $formData['create_worker'] && $subcontractor) {
                    $existingWorker = $subcontractor->workers()->first();

                    if ($existingWorker) {
                        // Update existing worker
                        $existingWorker->update($formData['worker']);
                        $worker = $existingWorker;
                    } else {
                        // Create new worker
                        $workerData = $formData['worker'];
                        $workerData['subcontractor_id'] = $subcontractor->id;
                        $workerData['worker_type'] = $subcontractor->business_type;
                        $worker = Worker::create($workerData);
                    }

                    // Handle Rates
                    if (isset($formData['worker_rates']) && is_array($formData['worker_rates'])) {
                        $newRateIds = collect($formData['worker_rates'])
                            ->filter(fn($rate) => isset($rate['id']) && $rate['id'])
                            ->pluck('id')
                            ->toArray();

                        $worker->rates()
                            ->when(!empty($newRateIds), function ($query) use ($newRateIds) {
                                $query->whereNotIn('id', $newRateIds);
                            })
                            ->delete();

                        foreach ($formData['worker_rates'] as $rateData) {
                            if (empty($rateData['name']) || empty($rateData['rate_amount'])) {
                                continue; // Skip incomplete rates
                            }

                            if (isset($rateData['id']) && $rateData['id']) {
                                $worker->rates()->where('id', $rateData['id'])->update($rateData);
                            } else {
                                $rateData['rateable_type'] = Worker::class;
                                $rateData['rateable_id'] = $worker->id;
                                $rateData['worker_id'] = $worker->id;
                                $worker->rates()->create($rateData);
                            }
                        }
                    }
                } elseif ($subcontractor && $subcontractor->workers()->exists()) {
                    // Remove worker if create_worker is false and worker exists
                    $subcontractor->workers()->delete();
                }
            }

            return $record;
        });
    }
}
