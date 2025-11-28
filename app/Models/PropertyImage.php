<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    // Allow mass assignment
    protected $fillable = [
        'property_id',
        'image_path',
    ];

    // Relationship: an image belongs to a property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
