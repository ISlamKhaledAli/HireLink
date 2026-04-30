<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class JobApprovedNotification extends Notification
{
    use Queueable;

    protected $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

   
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Job Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your job "' . $this->job->title . '" has been approved and is now live.')
            ->line('Candidates can now view and apply for your job.')
            ->action('View Job', url('/jobs/' . $this->job->id))
            ->line('Thank you for using our platform!');
    }

    
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_approved',
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => 'Your job "' . $this->job->title . '" has been approved and is now live.',
        ];
    }
}