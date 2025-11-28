<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyReservation extends Model
{
    protected $fillable = ['property_id', 'user_id', 'reserved_at'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
