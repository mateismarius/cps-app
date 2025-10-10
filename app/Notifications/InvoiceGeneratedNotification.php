<?php

// app/Notifications/InvoiceGeneratedNotification.php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGeneratedNotification extends Notification
{
    use Queueable;

    protected Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Invoice: ' . $this->invoice->invoice_number)
            ->line('A new invoice has been generated for you.')
            ->line('Invoice Number: ' . $this->invoice->invoice_number)
            ->line('Amount: £' . number_format($this->invoice->total_amount, 2))
            ->line('Due Date: ' . $this->invoice->due_date->format('d/m/Y'))
            ->action('View Invoice', url('/admin/invoices/' . $this->invoice->id))
            ->line('Please process payment by the due date.')
            ->line('Thank you for your business!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total_amount' => $this->invoice->total_amount,
            'due_date' => $this->invoice->due_date->format('d/m/Y'),
        ];
    }
}
