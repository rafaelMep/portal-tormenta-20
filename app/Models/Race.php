<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'size',
        'speed',
        'creature_type',
        'summary',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function attributeMods(): HasMany
    {
        return $this->hasMany(RaceAttributeMod::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(RaceVariant::class);
    }

    public function choiceSets(): HasMany
    {
        return $this->hasMany(RaceChoiceSet::class);
    }

    public function choiceGroups(): HasMany
    {
        return $this->hasMany(RaceChoiceGroup::class);
    }
}
