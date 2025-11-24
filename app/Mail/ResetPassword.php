<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

    private $user;
    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $email = strtolower(trim($this->user->email));
        $queryString = http_build_query(['id' => $this->user->id]);

        $personalization = [
            new Personalization($email,[
                'name' => $this->user->name,
                'email' => $email,
                'reset_link' => 'https://service.citio.cool/reset-password?'.$queryString,
                'support_email' => 'no-reply@support.citio.cool',
            ])
        ];

        info('Sending MailerSend to:', [
            'recipient' => $email,
            'personalization' => $personalization,
        ]);
        $recipients = [$email];
        return $this->mailerSend('o65qngkv808lwr12', $recipients,$personalization);
    }
}
