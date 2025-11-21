<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaceChoiceSetOption extends Model
{
    protected $table = 'race_choice_set_options';

    protected $fillable = [
        'set_id',
        'value',
        'label',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function set(): BelongsTo
    {
        return $this->belongsTo(RaceChoiceSet::class, 'set_id');
    }
}
