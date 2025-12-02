<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;

class ResetPasswordBrevo extends Notification
{
    use Queueable;

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
        // Laravel Mail facade is required but we'll override sending
        return ['mail'];
    }

    /**
     * Send the email using Brevo API.
     */
    public function toMail($notifiable)
    {
        $resetUrl = url("/reset-password/{$this->token}?email=".urlencode($notifiable->email));

        // Initialize Brevo API
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);

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

        try {
            $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            \Log::error("Brevo send email failed: " . $e->getMessage());
        }

        // Return a dummy MailMessage to satisfy Laravel
        return (new MailMessage)
            ->subject('Password Reset')
            ->line('If you are seeing this message, check the logs for Brevo API send.');
    }
}
