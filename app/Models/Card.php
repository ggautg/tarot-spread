<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'name', 'name_short', 'value', 'value_int',
        'type', 'suit', 'meaning_up', 'meaning_rev', 'desc'
    ];
}