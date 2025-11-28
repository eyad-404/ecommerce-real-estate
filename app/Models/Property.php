<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
    'category',
    'location',
    'price',
    'status',
    'image',
    'user_id',
    'description',
    'installment_years',
    'transaction_type',
];



    public function user() {
        return $this->belongsTo(User::class);
    }

    public function transactions() {
        return $this->hasOne(Transaction::class);
    }

    // For multiple images
    public function images() {
        return $this->hasMany(PropertyImage::class);
    }
    public function reservation()
    {
        return $this->hasOne(PropertyReservation::class);
    }


 public function isReserved()
    {
        return $this->reservation?->exists() ?? false;
    }

    // المستخدم الذي حجز
    public function reservedByUser()
    {
        return $this->reservation?->user;
    }   
}

