<?php

namespace App\Filament\Resources\Users\Pages;


use App\Filament\Resources\Users\UserResource;
use App\Models\Employee;
use App\Models\Rate;
use App\Models\Subcontractor;
use App\Models\User;
use App\Models\Worker;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove nested data that will be handled separately
        unset($data['employee']);
        unset($data['worker']);
        unset($data['worker_rates']);
        unset($data['new_subcontractor']);
        unset($data['create_worker']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Get the original form data
            $formData = $this->form->getState();

            // 1. Create the User
            $user = static::getModel()::create($data);

            // Assign roles if provided
            if (isset($formData['roles'])) {
                $user->syncRoles($formData['roles']);
            }

            // Assign permissions if provided
            if (isset($formData['permissions'])) {
                $user->syncPermissions($formData['permissions']);
            }

            // 2. Create Employee if user_type is employee
            if ($user->user_type === 'employee' && isset($formData['employee'])) {
                $employeeData = $formData['employee'];
                $employeeData['user_id'] = $user->id;

                $employee = Employee::create($employeeData);

                // 3. Create Worker if needed for employee
                if (isset($formData['create_worker']) && $formData['create_worker'] && isset($formData['worker'])) {
                    $workerData = $formData['worker'];
                    $workerData['employee_id'] = $employee->id;
                    $workerData['worker_type'] = 'employee';

                    $worker = Worker::create($workerData);

                    // 4. Create Rates if provided
                    if (isset($formData['worker_rates']) && is_array($formData['worker_rates']) && !empty($formData['worker_rates'])) {
                        foreach ($formData['worker_rates'] as $rateData) {
                            if (empty($rateData['name']) || empty($rateData['rate_amount'])) {
                                continue; // Skip incomplete rates
                            }

                            $rateData['rateable_type'] = Worker::class;
                            $rateData['rateable_id'] = $worker->id;
                            $rateData['worker_id'] = $worker->id;

                            $worker->rates()->create($rateData);
                        }
                    }
                }
            }

            // 5. Handle Subcontractor
            if (in_array($user->user_type, ['subcontractor_ltd', 'self_employed'])) {
                $subcontractor = null;

                // Check if creating new subcontractor
                if (isset($formData['new_subcontractor']) && !empty($formData['new_subcontractor']['name'])) {
                    $subcontractorData = $formData['new_subcontractor'];
                    $subcontractorData['business_type'] = $user->user_type === 'subcontractor_ltd' ? 'ltd' : 'self_employed';

                    $subcontractor = Subcontractor::create($subcontractorData);

                    // Link user to new subcontractor
                    $user->update(['subcontractor_id' => $subcontractor->id]);
                } elseif ($user->subcontractor_id) {
                    // Already linked via subcontractor_id
                    $subcontractor = $user->subcontractor;
                }

                // Create Worker for subcontractor if needed
                if (isset($formData['create_worker']) && $formData['create_worker'] && isset($formData['worker']) && $subcontractor) {
                    $workerData = $formData['worker'];
                    $workerData['subcontractor_id'] = $subcontractor->id;
                    $workerData['worker_type'] = $subcontractor->business_type;

                    $worker = Worker::create($workerData);

                    // Create Rates if provided
                    if (isset($formData['worker_rates']) && is_array($formData['worker_rates']) && !empty($formData['worker_rates'])) {
                        foreach ($formData['worker_rates'] as $rateData) {
                            if (empty($rateData['name']) || empty($rateData['rate_amount'])) {
                                continue; // Skip incomplete rates
                            }

                            $rateData['rateable_type'] = Worker::class;
                            $rateData['rateable_id'] = $worker->id;
                            $rateData['worker_id'] = $worker->id;

                            $worker->rates()->create($rateData);
                        }
                    }
                }
            }

            return $user;
        });
    }
}
