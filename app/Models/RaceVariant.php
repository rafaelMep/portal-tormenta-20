<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceVariant extends Model
{
    protected $fillable = ['race_id', 'key', 'name', 'summary', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function race()
    {
        return $this->belongsTo(Race::class);
    }
}
