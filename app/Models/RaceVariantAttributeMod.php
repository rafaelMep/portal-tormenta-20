<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaceVariantAttributeMod extends Model
{
    protected $table = 'race_variant_attribute_mods';

    protected $fillable = [
        'race_variant_id',
        'choice_option_id',
        'mode',
        'attribute',
        'modifier',
        'quantity',
        'exclusions',
        'notes',
    ];

    protected $casts = [
        'exclusions' => 'array',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(RaceVariant::class, 'race_variant_id');
    }

    public function choiceOption(): BelongsTo
    {
        return $this->belongsTo(RaceChoiceOption::class, 'choice_option_id');
    }
}
