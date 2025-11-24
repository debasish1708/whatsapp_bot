<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpToAdminNotification extends Notification
{
    use Queueable;
    protected $otp;
    /**
     * Create a new notification instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
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
            ->subject('Your Login OTP')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We have received a login request for your admin account.')
            ->line("Please use the following **One-Time Password (OTP)** to complete your login process:")
            ->line('**' . $this->otp . '**') // Assuming $notifiable->otp holds the OTP
            ->line('**Important:** This OTP is valid for **10 minutes only**. After that, it will expire and you will need to request a new one.')
            ->line('For your security, please do not share this OTP with anyone.')
            ->line('If you did not attempt to log in, you can safely ignore this email.')
            ->salutation('Regards, The CITIO Team');
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
