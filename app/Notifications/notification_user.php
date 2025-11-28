<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPurchaseRequest extends Notification
{
    use Queueable;

    public Transaction $transaction;

    // 🔹constructor
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    // 🔹قنوات الإشعار (database فقط)
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // 🔹شكل البيانات اللي هتتخزن في جدول notifications
    public function toDatabase(object $notifiable): array
    {
        $this->transaction->loadMissing(['buyer', 'property']);

        $buyerName = $this->transaction->buyer->name ?? 'مستخدم مجهول';
        $propertyTitle = $this->transaction->property->category . ' في ' . $this->transaction->property->location;

        return [
            'transaction_id' => $this->transaction->id,
            'property_id' => $this->transaction->property_id,
            'buyer_name' => $buyerName,
            'message' => "طلب شراء جديد للعقار: {$propertyTitle}",
            'price' => $this->transaction->price,
        ];
    }
}