<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceChoiceOption extends Model
{
    protected $table = 'race_choice_options';

    protected $fillable = [
        'group_id',
        'set_id',
        'key',
        'name',
        'summary',
        'meta',
        'sort',
        'is_official',
        'created_by_id',
        'value',
        'label'
    ];

    protected $casts = [
        'meta' => 'array',
        'is_official' => 'boolean',
    ];

    public function set()
    {
        return $this->belongsTo(RaceChoiceSet::class,   'set_id');
    }
    public function group()
    {
        return $this->belongsTo(RaceChoiceGroup::class, 'group_id');
    }
}
