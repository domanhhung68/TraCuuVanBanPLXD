<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', ['token' => $this->token, 'email' => $this->email], false));

        return (new MailMessage)
            ->subject('Đặt lại mật khẩu')
            ->greeting('Xin chào!')
            ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.')
            ->action('Đặt lại mật khẩu', $resetUrl)
            ->line('Link này sẽ hết hạn sau 60 phút.')
            ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.')
            ->line('Cảm ơn bạn đã sử dụng ứng dụng của chúng tôi!');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
