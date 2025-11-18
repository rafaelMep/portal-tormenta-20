<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceChoiceGroup extends Model
{
    protected $fillable = ['race_id', 'key', 'name', 'min_choices', 'max_choices', 'required', 'sort', 'meta'];
    protected $casts = ['meta' => 'array', 'required' => 'boolean'];

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function options()
    {
        return $this->hasMany(RaceChoiceOption::class, 'group_id')->orderBy('sort');
    }
}
