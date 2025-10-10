<?php
// app/Notifications/ScheduleCreatedNotification.php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleCreatedNotification extends Notification
{
    use Queueable;

    protected Schedule $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Schedule Assignment')
            ->line('You have been scheduled for a new shift.')
            ->line('Project: ' . $this->schedule->project->name)
            ->line('Date: ' . $this->schedule->schedule_date->format('d/m/Y'))
            ->line('Shift: ' . ucfirst($this->schedule->shift_type))
            ->line('Time: ' . $this->schedule->start_time . ' - ' . $this->schedule->end_time)
            ->line('Role: ' . ucfirst(str_replace('_', ' ', $this->schedule->role)))
            ->action('View Schedule', url('/admin/schedules/' . $this->schedule->id))
            ->line('Thank you!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'schedule_id' => $this->schedule->id,
            'project_name' => $this->schedule->project->name,
            'schedule_date' => $this->schedule->schedule_date->format('d/m/Y'),
            'shift_type' => $this->schedule->shift_type,
        ];
    }
}
