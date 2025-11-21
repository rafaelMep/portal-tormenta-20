<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaceChoiceSet extends Model
{
    protected $table = 'race_choice_sets';

    protected $fillable = [
        'race_id',
        'key',
        'label',
        'min_picks',
        'max_picks',
        'constraints',
        'meta',
    ];

    protected $casts = [
        'constraints' => 'array',
        'meta' => 'array',
    ];

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(RaceChoiceSetOption::class, 'set_id');
    }
}
