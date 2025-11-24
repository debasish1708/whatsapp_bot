<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectBusinessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Rejection Notice')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line("Thank you for registering with us using {$notifiable->email}.")
            ->line("After reviewing your account request, we regret to inform you that we are unable to approve your account at this time because: **{$notifiable->reject_reason}**.")
            ->line("This decision may be due to incomplete or unverifiable information. If you believe this was a mistake or wish to reapply, please contact our support team or submit additional details for review.")
            ->line('We appreciate your interest and hope to assist you in the future.')
            ->salutation('Kind regards, The CITIO Team')
            ->action('Visit Citio', 'https://service.citio.cool');
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
