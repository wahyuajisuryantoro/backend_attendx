<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class verifyEmail extends Notification
{
    use Queueable;
    protected $token;
    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verifUrl = url('/verif/email/' . $this->token . '?email=' . $notifiable->email);

        return (new MailMessage)
            ->subject('Verifikasi Email - ATTENDX')
            ->greeting('Halo ' . $notifiable->full_name . '!')
            ->line('Kami menerima permintaan verifikasi email untuk akun Anda.')
            ->action('verifikasi email', $verifUrl)
            ->line('Tautan ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak meminta verifikasi email, abaikan email ini.')
            ->salutation('Salam, ATTENDX');
    }

}
