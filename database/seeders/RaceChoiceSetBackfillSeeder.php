<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaceChoiceSetBackfillSeeder extends Seeder
{
    public function run(): void
    {
        // slugs que existem no seu RaceSeeder
        $map = [
            // Humano: +1 em 3 atributos (qualquer)
            'humano' => [
                'key'        => 'attr_plus1_x3_any',
                'label'      => '+1 em 3 atributos',
                'min_picks'  => 3,
                'max_picks'  => 3,
                'constraints' => null,          // sem exclusão
            ],
            // Sereia/Tritão: +1 em 3 atributos (qualquer)
            'sereia-tritao' => [
                'key'        => 'attr_plus1_x3_any',
                'label'      => '+1 em 3 atributos',
                'min_picks'  => 3,
                'max_picks'  => 3,
                'constraints' => null,
            ],
            // Lefou: +1 em 3 atributos, exceto CAR (CAR -1 já está como mod fixo)
            'lefou' => [
                'key'        => 'attr_plus1_x3_except_CAR',
                'label'      => '+1 em 3 atributos (exceto Carisma)',
                'min_picks'  => 3,
                'max_picks'  => 3,
                'constraints' => ['exclude' => ['CAR']],
            ],
            // Osteon: +1 em 3 atributos, exceto CON (CON -1 já está como mod fixo)
            'osteon' => [
                'key'        => 'attr_plus1_x3_except_CON',
                'label'      => '+1 em 3 atributos (exceto Constituição)',
                'min_picks'  => 3,
                'max_picks'  => 3,
                'constraints' => ['exclude' => ['CON']],
            ],
        ];

        $ALL_ATTRS = ['FOR', 'DES', 'CON', 'INT', 'SAB', 'CAR'];

        foreach ($map as $slug => $cfg) {
            $raceId = DB::table('races')->where('slug', $slug)->value('id');
            if (!$raceId) continue;

            // existe set p/ essa raça+key?
            $setId = DB::table('race_choice_sets')
                ->where('race_id', $raceId)
                ->where('key', $cfg['key'])
                ->value('id');

            if (!$setId) {
                $setId = DB::table('race_choice_sets')->insertGetId([
                    'race_id'     => $raceId,
                    'key'         => $cfg['key'],
                    'label'       => $cfg['label'],
                    'min_picks'   => $cfg['min_picks'],
                    'max_picks'   => $cfg['max_picks'],
                    'constraints' => $cfg['constraints'] ? json_encode($cfg['constraints']) : null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // cria opções (omitindo atributos excluídos, se houver)
            $excludes = collect($cfg['constraints']['exclude'] ?? [])->all();
            foreach ($ALL_ATTRS as $attr) {
                if (in_array($attr, $excludes, true)) continue;

                $exists = DB::table('race_choice_set_options')
                    ->where('set_id', $setId)
                    ->where('value', $attr)
                    ->exists();

                if (!$exists) {
                    DB::table('race_choice_set_options')->insert([
                        'set_id'     => $setId,
                        'value'      => $attr,
                        'label'      => $attr,
                        'meta'       => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
