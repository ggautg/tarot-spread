<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingCard extends Model
{
    protected $fillable = ['reading_id', 'spread_position_id', 'card_id', 'invertida'];

    protected $casts = ['invertida' => 'boolean'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function position()
    {
        return $this->belongsTo(SpreadPosition::class, 'spread_position_id');
    }
}