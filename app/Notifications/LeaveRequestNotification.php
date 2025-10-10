<?php
// app/Notifications/LeaveRequestNotification.php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestNotification extends Notification
{
    use Queueable;

    protected LeaveRequest $leaveRequest;
    protected string $action;

    public function __construct(LeaveRequest $leaveRequest, string $action = 'submitted')
    {
        $this->leaveRequest = $leaveRequest;
        $this->action = $action;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage);

        switch ($this->action) {
            case 'submitted':
                $message->subject('New Leave Request')
                    ->line('A new leave request has been submitted.')
                    ->line('Employee: ' . $this->leaveRequest->employee->full_name);
                break;
            case 'approved':
                $message->subject('Leave Request Approved')
                    ->line('Your leave request has been approved.');
                break;
            case 'rejected':
                $message->subject('Leave Request Rejected')
                    ->line('Your leave request has been rejected.')
                    ->line('Reason: ' . $this->leaveRequest->rejection_reason);
                break;
        }

        return $message
            ->line('Leave Type: ' . ucfirst($this->leaveRequest->leave_type))
            ->line('From: ' . $this->leaveRequest->start_date->format('d/m/Y'))
            ->line('To: ' . $this->leaveRequest->end_date->format('d/m/Y'))
            ->line('Days: ' . $this->leaveRequest->days_requested)
            ->action('View Request', url('/admin/leave-requests/' . $this->leaveRequest->id))
            ->line('Thank you!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'employee_name' => $this->leaveRequest->employee->full_name,
            'leave_type' => $this->leaveRequest->leave_type,
            'start_date' => $this->leaveRequest->start_date->format('d/m/Y'),
            'end_date' => $this->leaveRequest->end_date->format('d/m/Y'),
            'action' => $this->action,
        ];
    }
}
