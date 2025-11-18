<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'size',
        'speed',
        'creature_type',
        'source',
        'summary',
        'meta',
        'is_official',
        'created_by_id'
    ];

    protected $casts = [
        'meta' => 'array',
        'is_official' => 'boolean',
    ];

    public function variants()
    {
        return $this->hasMany(RaceVariant::class);
    }

    public function attributeMods()
    {
        return $this->hasMany(RaceAttributeMod::class);
    }

    public function choiceSets()
    {
        return $this->hasMany(RaceChoiceSet::class);
    }

    public function choiceGroups()
    {
        return $this->hasMany(RaceChoiceGroup::class);
    }
}
