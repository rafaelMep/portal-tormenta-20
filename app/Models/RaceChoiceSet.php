<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceChoiceSet extends Model
{
    protected $fillable = ['race_id', 'key', 'label', 'min_picks', 'max_picks', 'constraints'];
    protected $casts = ['constraints' => 'array'];

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function options()
    {
        return $this->hasMany(RaceChoiceOption::class, 'set_id')->orderBy('value');
    }
}
