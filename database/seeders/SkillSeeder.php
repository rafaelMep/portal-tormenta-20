<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['Acrobacia',        'acrobacia',       'DES', false, true,  'skill', false,  1],
            ['Adestramento',     'adestramento',    'CAR', true,  false, 'skill', false,  2],
            ['Atletismo',        'atletismo',       'FOR', false, false, 'skill', false,  3],
            ['Atuação',          'atuacao',         'CAR', true,  false, 'skill', false,  4],
            ['Cavalgar',         'cavalgar',        'DES', false, false, 'skill', false,  5],
            ['Conhecimento',     'conhecimento',    'INT', true,  false, 'skill', false,  6],
            ['Cura',             'cura',            'SAB', false, false, 'skill', false,  7],
            ['Diplomacia',       'diplomacia',      'CAR', false, false, 'skill', false,  8],
            ['Enganação',        'enganacao',       'CAR', false, false, 'skill', false,  9],
            ['Fortitude',        'fortitude',       'CON', false, false, 'save',  false, 10],
            ['Furtividade',      'furtividade',     'DES', false, true,  'skill', false, 11],
            ['Guerra',           'guerra',          'INT', true,  false, 'skill', false, 12],
            ['Iniciativa',       'iniciativa',      'DES', false, false, 'skill', false, 13],
            ['Intimidação',      'intimidacao',     'CAR', false, false, 'skill', false, 14],
            ['Intuição',         'intuicao',        'SAB', false, false, 'skill', false, 15],
            ['Investigação',     'investigacao',    'INT', false, false, 'skill', false, 16],
            ['Jogatina',         'jogatina',        'CAR', true,  false, 'skill', false, 17],
            ['Ladinagem',        'ladinagem',       'DES', true,  true,  'skill', false, 18],
            ['Luta',             'luta',            'FOR', false, false, 'attack', false, 19],
            ['Misticismo',       'misticismo',      'INT', true,  false, 'skill', false, 20],
            ['Nobreza',          'nobreza',         'INT', true,  false, 'skill', false, 21],
            ['Ofício',           'oficio',          'INT', true,  false, 'skill', true,  22],
            ['Percepção',        'percepcao',       'SAB', false, false, 'skill', false, 24],
            ['Pilotagem',        'pilotagem',       'DES', true,  false, 'skill', false, 25],
            ['Pontaria',         'pontaria',        'DES', false, false, 'attack', false, 26],
            ['Reflexos',         'reflexos',        'DES', false, false, 'save',  false, 27],
            ['Religião',         'religiao',        'SAB', true,  false, 'skill', false, 28],
            ['Sobrevivência',    'sobrevivencia',   'SAB', false, false, 'skill', false, 29],
            ['Vontade',          'vontade',         'SAB', false, false, 'save',  false, 30],
        ];

        $now = now();
        DB::table('skills')->upsert(
            array_map(fn($s) => [
                'name' => $s[0],
                'slug' => $s[1],
                'attr_key' => $s[2],
                'trained_only' => $s[3],
                'armor_penalty' => $s[4],
                'type' => $s[5],
                'has_specialization' => $s[6],
                'sort_order' => $s[7],
                'created_at' => $now,
                'updated_at' => $now,
            ], $skills),
            ['slug'], // chave de conflito
            ['name', 'attr_key', 'trained_only', 'armor_penalty', 'type', 'has_specialization', 'sort_order', 'updated_at']
        );
    }
}
