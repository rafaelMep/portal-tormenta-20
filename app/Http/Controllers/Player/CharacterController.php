<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Race;
use App\Models\Skill;

class CharacterController extends Controller
{
    public function create()
    {
        $races = Race::query()
            ->select(['id', 'slug', 'name', 'size', 'speed', 'creature_type', 'summary', 'meta', 'is_official'])
            ->with([
                // variantes simples (ex.: Nagah macho/fêmea, Moreau heranças, etc.)
                'variants:id,race_id,key,name,summary,meta',

                // modificadores de atributo da raça (e opcionais por escolha)
                'attributeMods:id,race_id,choice_option_id,mode,attribute,modifier,quantity,exclusions,notes',

                // “+1 em 3 atributos”, “+1 exceto CAR”, etc.
                'choiceSets:id,race_id,key,label,min_picks,max_picks,constraints',
                'choiceSets.options:id,set_id,value,label,meta',

                // grupos complexos (Golem: chassis/energy/size/marvel; Suraggel: reinos; etc.)
                'choiceGroups:id,race_id,key,name,min_choices,max_choices,required,sort,meta',
                'choiceGroups.options:id,group_id,key,name,summary,meta,sort,is_official',
            ])
            ->orderBy('name')
            ->get();

        $skills = Skill::query()
            ->select(['id', 'slug', 'name', 'abbr', 'ability', 'meta'])
            ->orderBy('name')->get();

        return Inertia::render('Player/Characters/Create', [
            'races'  => $races,
            'skills' => $skills,
        ]);
    }
}
