<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['slug', 'name', 'abbr', 'ability', 'meta'];
    protected $casts = ['meta' => 'array'];
}
