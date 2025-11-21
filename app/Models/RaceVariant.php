<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaceVariant extends Model
{
    protected $table = 'race_variants';

    protected $fillable = [
        'race_id',
        'key',
        'name',
        'summary',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function attributeMods(): HasMany
    {
        return $this->hasMany(RaceVariantAttributeMod::class, 'race_variant_id');
    }
}
