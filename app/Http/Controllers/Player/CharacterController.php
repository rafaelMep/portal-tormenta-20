<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use Inertia\Inertia;
use App\Models\Race;
use App\Models\Skill;
use App\Http\Resources\Races\RaceResource;

class CharacterController extends Controller
{
    public function create()
    {
        $races = Race::query()
            ->with([
                'attributeMods',
                'variants.attributeMods',
                'choiceSets.options',
                'choiceGroups.options',
            ])
            ->orderBy('name')
            ->get();

        $skills = Skill::orderBy('name')->get();

        return Inertia::render('Player/Characters/Create', [
            'races'  => RaceResource::collection($races),
            'skills' => SkillResource::collection($skills),
        ]);
    }
}
