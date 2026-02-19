<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory;

protected $fillable = [
  'user_id','name','address','city','lat','lng','price_per_hour',
  'equipments','available_slots','description','is_active','cover_image_path'
];


    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'price_per_hour' => 'decimal:2',
        'equipments' => 'array',
        'available_slots' => 'array',
        'is_active' => 'boolean',
        'cover_image_path' => 'string',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function slots()
    {
        return $this->hasMany(\App\Models\StudioSlot::class);
    }
}

