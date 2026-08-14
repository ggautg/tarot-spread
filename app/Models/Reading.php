<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reading extends Model
{
    protected $fillable = [
        'uuid', 'spread_id', 'user_id', 'session_id', 'pregunta', 'notas_personales',
        'server_seed', 'server_seed_hash', 'client_seed', 'revealed_at',
    ];

    protected static function booted()
    {
        static::creating(function ($reading) {
            $reading->uuid = $reading->uuid ?? (string) Str::uuid();
        });
    }

    public function spread()
    {
        return $this->belongsTo(Spread::class);
    }

    public function cards()
    {
        return $this->hasMany(ReadingCard::class)->orderBy('spread_position_id');
    }
}
