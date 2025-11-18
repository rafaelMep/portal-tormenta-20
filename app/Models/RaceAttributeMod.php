<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceAttributeMod extends Model
{
    protected $table = 'race_attribute_mods';

    protected $fillable = [
        'race_id',
        'choice_option_id',
        'mode',
        'attribute',
        'modifier',
        'quantity',
        'exclusions',
        'notes'
    ];

    protected $casts = [
        'exclusions' => 'array',
    ];

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    // quando o mod depende de uma escolha dentro de um grupo/set
    public function choiceOption()
    {
        return $this->belongsTo(RaceChoiceOption::class, 'choice_option_id');
    }
}
