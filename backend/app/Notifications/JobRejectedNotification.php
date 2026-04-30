<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class JobRejectedNotification extends Notification
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
            ->subject('Job Application Update')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately, your job "' . $this->job->title . '" was not approved at this time.')
            ->line('You can review your submission, improve it, and submit again.')
            ->action('Submit a New Job', url('/jobs/create'))
            ->line('We encourage you to try again. Good luck!');
    }

   
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_rejected',
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => 'Your job "' . $this->job->title . '" was not approved. You can improve it and submit again.',
        ];
    }
}