<?php

namespace App\Models;

use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    protected $casts = [
        'type' => ResourceType::class,
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
