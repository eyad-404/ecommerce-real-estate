<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;

class ResetPasswordBrevo extends Notification 
{
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // لازم Laravel Mail facade يكون موجود عشان Notification
        return ['mail'];
    }

    /**
     * Send the email using Brevo API.
     */
    public function toMail($notifiable)
    {
        // URL لإعادة تعيين كلمة المرور
        $resetUrl = url("/reset-password/{$this->token}?email=".urlencode($notifiable->email));

        // إعداد Brevo API
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

        // محتوى الرسالة
        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => 'Reset Your Password - EL Kayan',
            'sender' => [
                'name' => env('MAIL_FROM_NAME', 'EL Kayan'),
                'email' => env('MAIL_FROM_ADDRESS')
            ],
            'to' => [
                ['email' => $notifiable->email, 'name' => $notifiable->name]
            ],
            'htmlContent' => "<p>Hello {$notifiable->name},</p>
                              <p>Click <a href='{$resetUrl}'>here</a> to reset your password.</p>"
        ]);

        // محاولة إرسال البريد
        try {
            $response = $apiInstance->sendTransacEmail($sendSmtpEmail);
            \Log::info('Brevo email sent successfully', [
                'to' => $notifiable->email,
                'response' => $response
            ]);
        } catch (\Exception $e) {
            \Log::error("Brevo send email failed: " . $e->getMessage(), [
                'to' => $notifiable->email
            ]);
        }

        // MailMessage dummy عشان Laravel Notification
        return (new MailMessage)
            ->subject('Password Reset')
            ->line('If you are seeing this message, check the logs for Brevo API send.');
    }
}
