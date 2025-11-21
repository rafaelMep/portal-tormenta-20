<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'attr_key',
        'trained_only',
        'armor_penalty',
        'abbr',
        'ability',
        'meta',
    ];

    protected $casts = [
        'trained_only' => 'boolean',
        'armor_penalty' => 'boolean',
        'meta' => 'array',
    ];
}
