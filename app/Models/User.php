<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birth_date',
        'gender',
        'location',
        'phone',
        'role',
        'reserved_property_id',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function reservedProperties()
    {
        return $this->hasMany(Property::class, 'reserved_by');
    }

    public function reservedProperty()
    {
        return $this->belongsTo(Property::class, 'reserved_property_id');
    }

    protected function profileImageUrl(): Attribute
    {
        return Attribute::get(function () {
            $image = optional($this->profile)->profile_image;

            if ($image) {
                $path = public_path('images/profile/'.$image);

                if (File::exists($path)) {
                    return asset('images/profile/'.$image);
                }
            }

            return asset('images/default-profile.png');
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
