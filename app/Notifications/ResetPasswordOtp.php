<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordOtp extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $otp) {}

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
            ->subject('Kode OTP Reset Password - ROB Monitoring')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kamu menerima email ini karena ada permintaan reset password.')
            ->line('Gunakan kode OTP berikut untuk melanjutkan:')
            ->line('**' . $this->otp . '**')
            ->line('Kode ini berlaku selama **10 menit**.')
            ->line('Abaikan email ini jika kamu tidak merasa melakukan permintaan reset password.');
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
