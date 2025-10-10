<?php

namespace App\Observers;

use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestNotification;

class LeaveRequestObserver
{
    public function created(LeaveRequest $leaveRequest): void
    {
        // Notify managers about new leave request
        $managers = \App\Models\User::role(['super_admin', 'operations_manager', 'hr_manager'])->get();

        foreach ($managers as $manager) {
            $manager->notify(new LeaveRequestNotification($leaveRequest, 'submitted'));
        }
    }

    public function updated(LeaveRequest $leaveRequest): void
    {
        // Notify employee if status changed to approved or rejected
        if ($leaveRequest->isDirty('status')) {
            if (in_array($leaveRequest->status, ['approved', 'rejected'])) {
                $leaveRequest->employee->user->notify(
                    new LeaveRequestNotification($leaveRequest, $leaveRequest->status)
                );
            }
        }
    }
}
