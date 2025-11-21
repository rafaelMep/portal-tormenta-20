<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaceChoiceGroup extends Model
{
    protected $fillable = [
        'race_id',
        'key',
        'name',
        'min_choices',
        'max_choices',
        'required',
        'sort',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'required' => 'boolean',
    ];

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(RaceChoiceOption::class, 'group_id');
    }
}
