<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $applicationId;
    public $candidateName;
    public $amount;
    public $paidAt;
    /**
     * Create a new notification instance.
     */
    public function __construct($applicationId, $candidateName, $amount, $paidAt)
     {
        $this->applicationId = $applicationId;
        $this->candidateName = $candidateName;
        $this->amount = $amount;
        $this->paidAt = $paidAt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Successful - Candidate Unlocked')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your payment of $' . number_format($this->amount, 2) . ' was successful.')
            ->line('You have successfully unlocked the contact details for candidate: ' . $this->candidateName . '.')
            ->action('View Application', url(config('app.frontend_url') . '/admin/jobs'))
            ->line('Thank you for using HireLink!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_success',
            'application_id' => $this->applicationId,
            'candidate_name' => $this->candidateName,
            'amount' => $this->amount,
            'paid_at' => $this->paidAt,
        ];
    }
}
