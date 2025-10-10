<?php

// app/Notifications/CertificationExpiringNotification.php

namespace App\Notifications;

use App\Models\Certification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificationExpiringNotification extends Notification
{
    use Queueable;

    protected Certification $certification;

    public function __construct(Certification $certification)
    {
        $this->certification = $certification;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysUntilExpiry = now()->diffInDays($this->certification->expiry_date);

        return (new MailMessage)
            ->subject('Certification Expiring Soon')
            ->line('A certification is expiring soon and requires attention.')
            ->line('Certification: ' . $this->certification->name)
            ->line('Holder: ' . $this->certification->certifiable->name)
            ->line('Expiry Date: ' . $this->certification->expiry_date->format('d/m/Y'))
            ->line('Days Remaining: ' . $daysUntilExpiry)
            ->action('View Certification', url('/admin/certifications/' . $this->certification->id))
            ->line('Please ensure this certification is renewed before expiry.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'certification_id' => $this->certification->id,
            'certification_name' => $this->certification->name,
            'holder_type' => $this->certification->certifiable_type,
            'holder_name' => $this->certification->certifiable->name,
            'expiry_date' => $this->certification->expiry_date->format('d/m/Y'),
        ];
    }
}
