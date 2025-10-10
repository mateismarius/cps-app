<?php

// app/Notifications/TimesheetApprovedNotification.php

namespace App\Notifications;

use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimesheetApprovedNotification extends Notification
{
    use Queueable;

    protected Timesheet $timesheet;

    public function __construct(Timesheet $timesheet)
    {
        $this->timesheet = $timesheet;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Timesheet Approved')
            ->line('Your timesheet has been approved.')
            ->line('Project: ' . $this->timesheet->project->name)
            ->line('Date: ' . $this->timesheet->work_date->format('d/m/Y'))
            ->line('Hours: ' . $this->timesheet->hours_worked)
            ->line('Amount: £' . number_format($this->timesheet->calculateAmount(), 2))
            ->action('View Timesheet', url('/admin/timesheets/' . $this->timesheet->id))
            ->line('Thank you!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'timesheet_id' => $this->timesheet->id,
            'project_name' => $this->timesheet->project->name,
            'work_date' => $this->timesheet->work_date->format('d/m/Y'),
            'amount' => $this->timesheet->calculateAmount(),
        ];
    }
}
