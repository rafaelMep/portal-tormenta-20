<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RaceSeeder extends Seeder
{
    public function run(): void
    {
        // helper para inserir corrida + mods
        $insertRace = function (array $race, array $mods) {
            $raceId = DB::table('races')->insertGetId([
                'slug'          => $race['slug'] ?? Str::slug($race['name'], '-'),
                'name'          => $race['name'],
                'size'          => $race['size'] ?? 'Médio',
                'speed'         => $race['speed'] ?? null, // null = padrão do jogo (ex.: 9m)
                'creature_type' => $race['creature_type'] ?? 'humanoide',
                'source'        => $race['source'] ?? 'T20',
                'summary'       => $race['summary'] ?? null,
                'meta'          => isset($race['meta']) ? json_encode($race['meta']) : null,
                'created_at'    => now(),
                'updated_at'    => now(),
                'is_official'   => true,
                'created_by_id' => null,
            ]);

            foreach ($mods as $m) {
                DB::table('race_attribute_mods')->insert([
                    'race_id'    => $raceId,
                    'mode'       => $m['mode'] ?? 'fixed',
                    'attribute'  => $m['attribute'] ?? null,
                    'modifier'   => $m['modifier'],
                    'quantity'   => $m['quantity'] ?? 1,
                    'exclusions' => isset($m['exclusions']) ? json_encode($m['exclusions']) : null,
                    'notes'      => $m['notes'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        };

        $insertVariant = function (int $raceId, array $variant, array $mods) {
            $variantId = DB::table('race_variants')->insertGetId([
                'race_id'    => $raceId,
                'key'        => $variant['key'],            // ex.: 'coruja'
                'name'       => $variant['name'],           // ex.: 'Herança da Coruja'
                'summary'    => $variant['summary'] ?? null,
                'meta'       => isset($variant['meta']) ? json_encode($variant['meta']) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($mods as $m) {
                DB::table('race_variant_attribute_mods')->insert([
                    'race_variant_id' => $variantId,
                    'mode'            => $m['mode'] ?? 'fixed',
                    'attribute'       => $m['attribute'] ?? null,
                    'modifier'        => $m['modifier'],
                    'quantity'        => $m['quantity'] ?? 1,
                    'exclusions'      => isset($m['exclusions']) ? json_encode($m['exclusions']) : null,
                    'notes'           => $m['notes'] ?? null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        };

        $insertRace(
            [
                'name'          => 'Anão',
                'slug'          => 'anao',
                'size'          => 'Médio',
                'speed'         => 6,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => 'CON +2, SAB +1, DES -1. Visão no escuro; bônus subterrâneo; desloc. 6m sem penalidade por armadura/carga; PV bônus; proficiência nas armas anãs.',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'conhecimento_das_rochas',
                            'name'  => 'Conhecimento das Rochas',
                            'senses' => ['darkvision' => true],
                            'skill_bonuses' => [
                                ['skill' => 'percepcao',      'value' => 2, 'condition' => 'subterrâneo'],
                                ['skill' => 'sobrevivencia',  'value' => 2, 'condition' => 'subterrâneo'],
                            ],
                        ],
                        [
                            'key'   => 'devagar_e_sempre',
                            'name'  => 'Devagar e Sempre',
                            'ignore_speed_penalty' => ['armor', 'encumbrance'],
                        ],
                        [
                            'key'   => 'duro_como_pedra',
                            'name'  => 'Duro como Pedra',
                            'hp_bonus' => [
                                'level_1'   => 3,
                                'per_level' => 1,
                            ],
                        ],
                        [
                            'key'   => 'tradicao_de_heredrimm',
                            'name'  => 'Tradição de Heredrimm',
                            'weapon_training' => [
                                'treat_as_simple' => ['machados', 'martelos', 'marretas', 'picaretas'],
                                'attack_bonus'    => ['categories' => ['machados', 'martelos', 'marretas', 'picaretas'], 'value' => 2],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CON', 'modifier' => +2],
                ['attribute' => 'SAB', 'modifier' => +1],
                ['attribute' => 'DES', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Dahllan',
                'slug'          => 'dahllan',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => 'SAB +2, DES +1, INT -1. Amiga das Plantas; Armadura de Allihanna; Empatia Selvagem. (Apenas mulheres.)',
                'meta'          => [
                    'restrictions' => [
                        'sex' => 'female',
                    ],
                    'features' => [
                        [
                            'key'   => 'amiga_das_plantas',
                            'name'  => 'Amiga das Plantas',
                            'spell_like' => [
                                'spell'            => 'Controlar Plantas',
                                'attribute_key'    => 'SAB',
                                'pm_discount_if_known' => 1,
                            ],
                        ],
                        [
                            'key'   => 'armadura_de_allihanna',
                            'name'  => 'Armadura de Allihanna',
                            'active' => [
                                'action'   => 'movimento',
                                'cost_pm'  => 1,
                                'effects'  => [
                                    ['type' => 'defense_bonus', 'value' => 2, 'duration' => 'scene'],
                                ],
                            ],
                        ],
                        [
                            'key'   => 'empatia_selvagem',
                            'name'  => 'Empatia Selvagem',
                            'communication' => ['with' => 'animais'],
                            'uses_skill'    => 'adestramento',
                            'notes'         => 'Pode usar Adestramento para mudar atitude/persuadir animais; se obtida novamente, +2 em Adestramento.',
                            'skill_bonuses' => [
                                ['skill' => 'adestramento', 'value' => 2, 'condition' => 'apenas se receber novamente'],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'SAB', 'modifier' => +2],
                ['attribute' => 'DES', 'modifier' => +1],
                ['attribute' => 'INT', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Elfo',
                'slug'          => 'elfo',
                'size'          => 'Médio',
                'speed'         => 12,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => 'INT +2, DES +1, CON -1. Desloc. 12m; +1 PM por nível; visão na penumbra; +2 em Misticismo e Percepção.',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'graca_de_glorienn',
                            'name'  => 'Graça de Glórienn',
                            'speed_override' => 12,
                        ],
                        [
                            'key'   => 'sangue_magico',
                            'name'  => 'Sangue Mágico',
                            'pm_bonus' => [
                                'per_level' => 1,
                            ],
                        ],
                        [
                            'key'   => 'sentidos_elficos',
                            'name'  => 'Sentidos Élficos',
                            'senses' => ['low_light_vision' => true],
                            'skill_bonuses' => [
                                ['skill' => 'misticismo', 'value' => 2],
                                ['skill' => 'percepcao',  'value' => 2],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'INT', 'modifier' => +2],
                ['attribute' => 'DES', 'modifier' => +1],
                ['attribute' => 'CON', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Goblin',
                'slug'          => 'goblin',
                'size'          => 'Pequeno',
                'speed'         => 9,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => 'DES +2, INT +1, CAR -1. Engenhoso; visão no escuro; escalada = desloc. terrestre; Pequeno com 9m; +2 Fortitude; recuperação mínima = nível.',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'engenhoso',
                            'name'  => 'Engenhoso',
                            'tools' => [
                                'ignore_penalty_without_tools' => true,
                                'bonus_with_tools'             => 2,
                            ],
                            'notes' => 'Não sofre penalidade por falta de ferramentas; com ferramentas, +2 no teste de perícia.',
                        ],
                        [
                            'key'    => 'espelunqueiro',
                            'name'   => 'Espelunqueiro',
                            'senses' => ['darkvision' => true],
                            'speeds' => ['climb_equals_land' => true],
                        ],
                        [
                            'key'            => 'peste_esguia',
                            'name'           => 'Peste Esguia',
                            'size_override'  => 'Pequeno',
                            'notes'          => 'Tamanho Pequeno sem reduzir o deslocamento (permanece 9m).',
                        ],
                        [
                            'key'           => 'rato_das_ruas',
                            'name'          => 'Rato das Ruas',
                            'save_bonuses'  => ['fortitude' => 2],
                            'recovery'      => [
                                'hp_min_per_rest' => 'level',
                                'pm_min_per_rest' => 'level',
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'DES', 'modifier' => +2],
                ['attribute' => 'INT', 'modifier' => +1],
                ['attribute' => 'CAR', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Humano',
                'slug'          => 'humano',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => '+1 em três atributos diferentes. Versátil: 2 perícias à escolha (podem ser fora da classe); pode trocar uma por 1 poder geral.',
                'meta'          => [
                    'features' => [
                        [
                            'key'  => 'versatil',
                            'name' => 'Versátil',
                            'skill_training_choices'            => 2,
                            'allow_out_of_class_skills'         => true,
                            'may_exchange_one_for_general_power' => true,
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                [
                    'mode'      => 'choice',
                    'modifier'  => +1,
                    'quantity'  => 3,
                    'notes'     => 'Atributos devem ser distintos; a UI/validação deve impedir repetir o mesmo atributo.',
                ],
            ]
        );

        $insertRace(
            [
                'name'          => 'Hynne',
                'slug'          => 'hynne',
                'size'          => 'Pequeno',
                'speed'         => 6,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => 'DES +2, CAR +1, FOR −1. Arremessador; Pequeno (6m); +2 Enganação; Atletismo usa DES; 1 PM para refazer resistência.',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'arremessador',
                            'name'  => 'Arremessador',
                            'ranged_damage_step_bonus' => [
                                'categories' => ['fundas', 'armas_de_arremesso'],
                                'value'      => 1,
                            ],
                        ],
                        [
                            'key'           => 'pequeno_e_rechonchudo',
                            'name'          => 'Pequeno e Rechonchudo',
                            'size_override' => 'Pequeno',
                            'speed_override' => 6,
                            'skill_bonuses' => [
                                ['skill' => 'enganacao', 'value' => 2],
                            ],
                            'skill_key_overrides' => [
                                ['skill' => 'atletismo', 'use' => 'DES'],
                            ],
                        ],
                        [
                            'key'   => 'sorte_salvadora',
                            'name'  => 'Sorte Salvadora',
                            'reroll' => [
                                'type'     => 'saving_throw',
                                'cost_pm'  => 1,
                                'limit'    => 'per_attempt',
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'DES', 'modifier' => +2],
                ['attribute' => 'CAR', 'modifier' => +1],
                ['attribute' => 'FOR', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Kliren',
                'slug'          => 'kliren',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => 'INT +2, CAR +1, FOR -1. Híbrido (1 perícia treinada); Engenhosidade (2 PM para somar INT em perícia, não em ataque; se recebida de novo, -1 PM); Ossos Frágeis (impacto +1 por dado); Vanguardista (prof. armas de fogo; +2 em Ofício à escolha).',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'hibrido',
                            'name'  => 'Híbrido',
                            'skill_training_choices'    => 1,
                            'allow_out_of_class_skills' => true,
                        ],
                        [
                            'key'   => 'engenhosidade',
                            'name'  => 'Engenhosidade',
                            'active' => [
                                'trigger'  => 'on_skill_check',
                                'cost_pm'  => 2,
                                'effects'  => [
                                    ['type' => 'add_attribute_to_roll', 'attribute' => 'INT'],
                                ],
                                'notes'                   => 'Não se aplica a testes de ataque.',
                                'discount_if_reacquired'  => 1,
                            ],
                        ],
                        [
                            'key'   => 'ossos_frageis',
                            'name'  => 'Ossos Frágeis',
                            'vulnerability' => [
                                ['type' => 'impacto', 'extra_damage_per_die' => 1],
                            ],
                            'notes' => 'Sofre +1 de dano por dado de dano de impacto (ex.: 1d6 vira 1d6+1).',
                        ],
                        [
                            'key'   => 'vanguardista',
                            'name'  => 'Vanguardista',
                            'weapon_proficiencies' => ['armas_de_fogo'],
                            'skill_bonus_choice' => [
                                ['skill' => 'oficio', 'value' => 2, 'specialization_required' => true],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'INT', 'modifier' => +2],
                ['attribute' => 'CAR', 'modifier' => +1],
                ['attribute' => 'FOR', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Lefou',
                'slug'          => 'lefou',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => '+1 em três atributos (exceto CAR), CAR -1. Cria da Tormenta (+5 vs lefeu/Tormenta). Deformidade: +2 em duas perícias (ou troque uma por 1 Poder da Tormenta).',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'cria_da_tormenta',
                            'name'  => 'Cria da Tormenta',
                            'save_bonus_vs' => [
                                [
                                    'targets' => ['efeitos_de_lefeu', 'efeitos_da_tormenta'],
                                    'saving_throws' => ['fortitude', 'reflexos', 'vontade'],
                                    'value' => 5,
                                ],
                            ],
                            'tags' => ['tormenta', 'lefeu'],
                        ],
                        [
                            'key'   => 'deformidade',
                            'name'  => 'Deformidade',
                            'skill_bonus_choices' => [
                                'count' => 2,
                                'value' => 2,
                                'any_skill' => true,
                                'counts_as_torment_power' => true,
                                'no_extra_charisma_loss' => true,
                            ],
                            'may_exchange_one_for_torment_power' => true,
                            'notes' => 'Os dois bônus de perícia contam como Poderes da Tormenta (sem perda extra de CAR). Você pode trocar um deles por 1 Poder da Tormenta à escolha.',
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CAR', 'modifier' => -1],
                [
                    'mode'       => 'choice',
                    'modifier'   => +1,
                    'quantity'   => 3,
                    'exclusions' => ['CAR'],
                    'notes'      => 'Atributos devem ser distintos; não pode escolher CAR.',
                ],
            ]
        );

        $insertRace(
            [
                'name'          => 'Medusa',
                'slug'          => 'medusa',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => 'DES +2, CAR +1. Cria de Megalokk (visão no escuro); Natureza Venenosa (resist. veneno +5; envenenar arma, 1d12 de vida); Olhar Atordoante (mov + 1 PM, Fort CD CAR, 1/ cena). (Apenas mulheres.)',
                'meta'          => [
                    'restrictions' => [
                        'sex' => 'female',
                    ],
                    'features' => [
                        [
                            'key'    => 'cria_de_megalokk',
                            'name'   => 'Cria de Megalokk',
                            'senses' => ['darkvision' => true],
                            'tags'   => ['monstro'],
                        ],
                        [
                            'key'   => 'natureza_venenosa',
                            'name'  => 'Natureza Venenosa',
                            'resistances' => [
                                ['type' => 'poison', 'value' => 5],
                            ],
                            'active' => [
                                'action'  => 'movimento',
                                'cost_pm' => 1,
                                'weapon_coat' => [
                                    'tag'      => 'veneno',
                                    'damage'   => ['type' => 'poison', 'formula' => '1d12', 'applies' => 'on_hit'],
                                    'duration' => 'scene_or_first_hit',
                                ],
                                'limit'   => 'no_limit',
                            ],
                        ],
                        [
                            'key'   => 'olhar_atordoante',
                            'name'  => 'Olhar Atordoante',
                            'active' => [
                                'action'  => 'movimento',
                                'cost_pm' => 1,
                                'target'  => ['range' => 'short', 'count' => 1],
                                'save'    => [
                                    'type' => 'fortitude',
                                    'dc'   => ['attribute' => 'CAR'],
                                ],
                                'on_fail' => [
                                    ['type' => 'condition', 'name' => 'atordoado', 'duration' => '1_round'],
                                ],
                                'limit' => 'once_per_scene',
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'DES', 'modifier' => +2],
                ['attribute' => 'CAR', 'modifier' => +1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Minotauro',
                'slug'          => 'minotauro',
                'size'          => 'Médio',
                'speed'         => 9, // padrão de médios
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => 'FOR +2, CON +1, SAB −1. Chifres (arma natural; ataque extra 1 PM); +1 Defesa; faro (não fica desprevenido vs. inimigos não vistos; camuflagem total 20% em alcance curto); medo de altura. (Apenas homens.)',
                'meta'          => [
                    'restrictions' => [
                        'sex' => 'male', // Apenas homens
                    ],
                    'features' => [
                        [
                            'key'   => 'chifres',
                            'name'  => 'Chifres',
                            'natural_weapons' => [
                                [
                                    'id'        => 'horns',
                                    'label'     => 'Chifres',
                                    'damage'    => ['formula' => '1d6', 'crit' => 'x2', 'type' => 'perfuração'],
                                ],
                            ],
                            // 1x por rodada, após "agredir" com outra arma, pode gastar 1 PM p/ ataque extra com os chifres
                            'active' => [
                                'trigger'            => 'on_agredir_with_other_weapon',
                                'cost_pm'            => 1,
                                'grant_extra_attack' => ['weapon' => 'horns', 'range' => 'melee'],
                                'limit'              => 'once_per_round',
                            ],
                        ],
                        [
                            'key'   => 'couro_rigido',
                            'name'  => 'Couro Rígido',
                            'defense_bonus' => 1, // +1 na Defesa
                        ],
                        [
                            'key'   => 'faro',
                            'name'  => 'Faro',
                            'senses' => ['scent' => true],
                            'combat_modifiers' => [
                                'not_flat_footed_against_unseen' => true,
                                'total_concealment_miss_chance'  => [
                                    'range' => 'short',
                                    'value' => 0.20, // 20%
                                ],
                            ],
                            'notes' => 'Contra inimigos que não possa ver, não fica desprevenido; camuflagem total causa apenas 20% de falha em alcance curto.',
                        ],
                        [
                            'key'   => 'medo_de_altura',
                            'name'  => 'Medo de Altura',
                            'condition_trigger' => [
                                'when_adjacent_to_drop' => '>=3m',
                                'apply' => ['condition' => 'abalado'],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'FOR', 'modifier' => +2],
                ['attribute' => 'CON', 'modifier' => +1],
                ['attribute' => 'SAB', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Osteon',
                'slug'          => 'osteon',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'morto-vivo',
                'source'        => 'T20',
                'summary'       => '+1 em três atributos (exceto CON), CON −1. Armadura Óssea (resist. corte/frio/perfuração 5); Memória Póstuma (1 perícia OU 1 poder geral OU herdar 1 habilidade de outra raça humanoide não-humana e seu tamanho); Natureza Esquelética (undead, visão no escuro, imunidades); Preço da Não Vida (descanso especial).',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'armadura_ossea',
                            'name'  => 'Armadura Óssea',
                            'resistances' => [
                                ['type' => 'slashing',  'value' => 5],
                                ['type' => 'piercing',  'value' => 5],
                                ['type' => 'cold',      'value' => 5],
                            ],
                        ],
                        [
                            'key'   => 'memoria_postuma',
                            'name'  => 'Memória Póstuma',
                            // Três caminhos possíveis; a UI pode oferecer um seletor de opção
                            'options' => [
                                'A' => [
                                    'label' => 'Treinamento',
                                    'skill_training_choices'    => 1,
                                    'allow_out_of_class_skills' => true,
                                    'or_general_power_choices'  => 1, // em vez da perícia, 1 poder geral
                                ],
                                'B' => [
                                    'label' => 'Herança de Raça',
                                    'heritage' => [
                                        'race_pool' => [
                                            'type' => 'humanoide',
                                            'exclude' => ['Humano'], // “outra raça humanoide que não humano”
                                        ],
                                        'inherit' => [
                                            'feature_choices' => 1,  // escolhe 1 habilidade dessa raça
                                            'size_if_different' => true, // herda tamanho se for diferente de Médio
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'key'    => 'natureza_esqueletica',
                            'name'   => 'Natureza Esquelética',
                            'type_override' => 'morto-vivo',
                            'senses' => ['darkvision' => true],
                            'immunities' => [
                                'fatigue',      // cansaço
                                'metabolic',    // efeitos metabólicos
                                'darkness',     // efeitos de trevas
                                'poison',       // veneno
                            ],
                            'physiology' => [
                                'no_breath' => true,
                                'no_food'   => true,
                                'no_sleep'  => true,
                            ],
                            'healing_interactions' => [
                                'positive_magic_heals' => false, // magias de cura causam dano
                                'positive_magic_harms' => true,
                                'darkness_damage_heals' => true, // dano de trevas recupera PV
                                'food_consumables_no_benefit' => true,
                            ],
                        ],
                        [
                            'key'   => 'preco_da_nao_vida',
                            'name'  => 'Preço da Não Vida',
                            'rest'  => [
                                'required_environment' => ['starlight', 'underground'], // luz de estrelas OU subterrâneo
                                'duration_hours' => 8,
                                'recovery' => [
                                    'pv_pm_recover' => 'normal_only',
                                    'ignore_rest_quality' => true, // não é afetado por boas/ruins condições de descanso
                                ],
                                'otherwise' => [
                                    'apply_condition' => 'fome',
                                ],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CON', 'modifier' => -1],
                [
                    'mode'       => 'choice',
                    'modifier'   => +1,
                    'quantity'   => 3,
                    'exclusions' => ['CON'],
                    'notes'      => 'Três atributos distintos; não pode escolher CON.',
                ],
            ]
        );

        $insertRace(
            [
                'name'          => 'Sereia/Tritão',
                'slug'          => 'sereia-tritao',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => '+1 em três atributos. Canção dos Mares (2 magias: Amedrontar/Comando/Despedaçar/Enfeitiçar/Hipnotismo/Sono; CD baseada em CAR; se reaprender, −1 PM). Mestre do Tridente (tridente é simples; +2 dano com azagaia/lança/tridente). Transformação Anfíbia (respira água; natação 12m na água; em terra 9m; sem contato diário com água não recupera PM).',
                'meta'          => [
                    'tags' => ['aquatico', 'anfibio'],
                    'features' => [
                        [
                            'key'   => 'cancao_dos_mares',
                            'name'  => 'Canção dos Mares',
                            'granted_spells_choices' => [
                                'count'      => 2,
                                'attribute'  => 'CAR',
                                'options'    => [
                                    ['name' => 'Amedrontar',  'slug' => 'amedrontar',  'circle' => 1],
                                    ['name' => 'Comando',     'slug' => 'comando',     'circle' => 1],
                                    ['name' => 'Despedaçar',  'slug' => 'despedacar',  'circle' => 2],
                                    ['name' => 'Enfeitiçar',  'slug' => 'enfeiticar',  'circle' => 1],
                                    ['name' => 'Hipnotismo',  'slug' => 'hipnotismo',  'circle' => 1],
                                    ['name' => 'Sono',        'slug' => 'sono',        'circle' => 1],
                                ],
                                'discount_if_relearned_pm' => 1,
                            ],
                        ],
                        [
                            'key'   => 'mestre_do_tridente',
                            'name'  => 'Mestre do Tridente',
                            'weapon_proficiencies_add' => ['tridente'],
                            'damage_bonuses' => [
                                ['category' => 'azagaia',  'value' => 2],
                                ['category' => 'lanca',    'value' => 2],
                                ['category' => 'tridente', 'value' => 2],
                            ],
                        ],
                        [
                            'key'   => 'transformacao_anfibia',
                            'name'  => 'Transformação Anfíbia',
                            'amphibious' => true,
                            'speeds' => [
                                'land' => 9,
                                'swim' => 12,
                            ],
                            'physiology' => [
                                'breathe_underwater' => true,
                            ],
                            'pm_recovery_requires_daily_water_contact' => true,
                            'notes' => 'Se ficar >1 dia sem contato com água, não recupera PM com descanso até se molhar.',
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                [
                    'mode'      => 'choice',
                    'modifier'  => +1,
                    'quantity'  => 3,
                    'notes'     => 'Escolha três atributos distintos para receber +1 cada.',
                ],
            ]
        );

        $insertRace(
            [
                'name'          => 'Qareen',
                'slug'          => 'qareen',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'espírito',
                'source'        => 'T20',
                'summary'       => 'CAR +2, INT +1, SAB -1. Desejos (-1 PM em magia “pedida” desde seu último turno). Resistência Elemental 10 conforme ascendência (água/frio, ar/eletricidade, fogo/fogo, terra/ácido, luz/luz, trevas/trevas). Tatuagem Mística (uma magia de 1º círculo à escolha; CHA; -1 PM se reaprender).',
                'meta'          => [
                    'tags' => ['gênio', 'planar'],
                    'features' => [
                        [
                            'key'   => 'desejos',
                            'name'  => 'Desejos',
                            'conditional_mana_discount' => [
                                'trigger'     => 'spell_requested_since_last_turn',
                                'discount_pm' => 1,
                                'limit'       => 'per_cast',
                            ],
                            'action_hint' => 'fazer um desejo ao qareen é ação livre',
                        ],
                        [
                            'key'   => 'resistencia_elemental',
                            'name'  => 'Resistência Elemental',
                            'choice' => [
                                'field'   => 'ascendencia',
                                'options' => [
                                    ['value' => 'agua',   'label' => 'Água',    'damage_reduction' => ['type' => 'frio',         'value' => 10]],
                                    ['value' => 'ar',     'label' => 'Ar',      'damage_reduction' => ['type' => 'eletricidade', 'value' => 10]],
                                    ['value' => 'fogo',   'label' => 'Fogo',    'damage_reduction' => ['type' => 'fogo',         'value' => 10]],
                                    ['value' => 'terra',  'label' => 'Terra',   'damage_reduction' => ['type' => 'ácido',        'value' => 10]],
                                    ['value' => 'luz',    'label' => 'Luz',     'damage_reduction' => ['type' => 'luz',          'value' => 10]],
                                    ['value' => 'trevas', 'label' => 'Trevas',  'damage_reduction' => ['type' => 'trevas',       'value' => 10]],
                                ],
                            ],
                        ],
                        [
                            'key'   => 'tatuagem_mistica',
                            'name'  => 'Tatuagem Mística',
                            'granted_spell_choice' => [
                                'count'     => 1,
                                'circle'    => 1,
                                'attribute' => 'CAR',
                                'discount_if_relearned_pm' => 1,
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CAR', 'modifier' => +2],
                ['attribute' => 'INT', 'modifier' => +1],
                ['attribute' => 'SAB', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Sílfide',
                'slug'          => 'silfide',
                'size'          => 'Minúsculo',
                'speed'         => 9,
                'creature_type' => 'espírito',
                'source'        => 'T20',
                'summary'       => 'CAR +2, DES +1, FOR -2. Asas de Borboleta (flutuar 9m, ignora terreno difícil, imune a queda; voo 12m por 1 PM/rodada). Espírito da Natureza (tipo espírito, visão na penumbra, fala com animais). Magia das Fadas (2 magias; CD baseada em CAR; se aprender depois, −1 PM).',
                'meta'          => [
                    'tags' => ['fada', 'feerico'],
                    'features' => [
                        [
                            'key'   => 'asas_de_borboleta',
                            'name'  => 'Asas de Borboleta',
                            'hover' => [
                                'height_m'                => 1.5,
                                'speed'                   => 9,
                                'ignore_difficult_terrain' => true,
                                'fall_damage_immunity'    => true,
                                'except_when_unconscious' => true,
                            ],
                            'active' => [
                                'action'        => 'per_round',
                                'cost_pm'       => 1,
                                'grant_flight'  => ['speed' => 12],
                                'limit'         => 'sustain_per_round',
                            ],
                        ],
                        [
                            'key'   => 'espirito_da_natureza',
                            'name'  => 'Espírito da Natureza',
                            'type_override' => 'espírito',
                            'senses'        => ['low_light_vision' => true],
                            'languages'     => [
                                ['can_speak_with' => 'animais', 'free' => true],
                            ],
                        ],
                        [
                            'key'   => 'magia_das_fadas',
                            'name'  => 'Magia das Fadas',
                            'granted_spells_choices' => [
                                'count'     => 2,
                                'attribute' => 'CAR',
                                'options'   => [
                                    ['name' => 'Criar Ilusão', 'slug' => 'criar-ilusao', 'circle' => 1],
                                    ['name' => 'Enfeitiçar',   'slug' => 'enfeiticar',   'circle' => 1],
                                    ['name' => 'Luz',          'slug' => 'luz',          'circle' => 0, 'as_arcane' => true],
                                    ['name' => 'Sono',         'slug' => 'sono',         'circle' => 1],
                                ],
                                'discount_if_relearned_pm' => 1,
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CAR', 'modifier' => +2],
                ['attribute' => 'DES', 'modifier' => +1],
                ['attribute' => 'FOR', 'modifier' => -2],
            ]
        );

        /* ======================
            Suraggel — base
        ====================== */
        $suraggelId = DB::table('races')->insertGetId([
            'slug'          => 'suraggel',
            'name'          => 'Suraggel',
            'size'          => 'Médio',
            'speed'         => null,               // padrão do sistema (ex.: 9m)
            'creature_type' => 'espírito',
            'source'        => 'T20',
            'summary'       => 'Descendentes planares; herança divina comum e variações Aggelus / Sulfure.',
            'meta'          => json_encode([
                // Herança Divina (comum a todos)
                'senses' => ['darkvision' => true],

                // Alternativas “DH” — podem substituir a habilidade principal da variante
                // (tanto "luz_sagrada" quanto "sombras_profanas")
                'dh_alternatives' => [
                    [
                        'key'       => 'al-gazara',
                        'name'      => 'Herança de Al-Gazara',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'attribute_random' => ['quantity' => 1] // +1 em 1 atributo aleatório
                        ],
                    ],
                    [
                        'key'       => 'arboria',
                        'name'      => 'Herança de Arbória',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'wild_shape' => [
                                'one_form' => true,
                                'forms_allowed' => ['Ágil', 'Sorrateira', 'Veloz'],
                                'relearn_cost_reduction_pm' => 1
                            ],
                        ],
                    ],
                    [
                        'key'       => 'chacina',
                        'name'      => 'Herança de Chacina',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'wild_shape' => [
                                'one_form' => true,
                                'forms_allowed' => ['Feroz', 'Resistente'],
                                'relearn_cost_reduction_pm' => 1
                            ],
                        ],
                    ],
                    [
                        'key'       => 'deathok',
                        'name'      => 'Herança de Deathok',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'skill_bonuses_choice' => [
                                'quantity' => 2,
                                'bonus' => 2,
                                'daily_reassign' => true
                            ],
                        ],
                    ],
                    [
                        'key'       => 'drashantyr',
                        'name'      => 'Herança de Drashantyr',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'pm_flat_bonus' => 1,
                            'resistances'   => [
                                ['type' => 'ácido', 'value' => 5],
                                ['type' => 'eletricidade', 'value' => 5],
                                ['type' => 'fogo', 'value' => 5],
                                ['type' => 'frio', 'value' => 5],
                                ['type' => 'luz', 'value' => 5],
                                ['type' => 'trevas', 'value' => 5],
                            ],
                        ],
                    ],
                    [
                        'key'       => 'kundali',
                        'name'      => 'Herança de Kundali',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'defense_bonus'            => 2,
                            'combat_maneuvers_bonus'   => 2,
                        ],
                    ],
                    [
                        'key'       => 'magika',
                        'name'      => 'Herança de Magika',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'magic' => [
                                'granted_spells' => [[
                                    'name'   => 'Arcana (1º círculo, à escolha)',
                                    'circle' => 1,
                                    'key'    => ['INT', 'CAR'],
                                    'school' => 'arcana'
                                ]],
                                'relearn_cost_reduction_pm' => 1
                            ],
                        ],
                    ],
                    [
                        'key'       => 'nivenciuen',
                        'name'      => 'Herança de Nivenciuén',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'skill_bonuses' => [
                                ['skill' => 'Misticismo', 'bonus' => 2],
                            ],
                            'elf_inheritance_choice' => ['Graça de Glórienn', 'Sangue Mágico'],
                        ],
                    ],
                    [
                        'key'       => 'odisseia',
                        'name'      => 'Herança de Odisseia',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'initiative_bonus' => 2,
                            'skill_bonuses'    => [['skill' => 'Percepção', 'bonus' => 2]],
                            'carry_slots_bonus' => 2,
                        ],
                    ],
                    [
                        'key'       => 'ordine',
                        'name'      => 'Herança de Ordine',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'skill_bonuses' => [
                                ['skill' => 'Intuição', 'bonus' => 2],
                                ['skill' => 'Investigação', 'bonus' => 2],
                            ],
                            'no_roll_bonus' => 2, // testes sem rolagem (0/10/20)
                        ],
                    ],
                    [
                        'key'       => 'pelagia',
                        'name'      => 'Herança de Pelágia',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'choose_skills_take10_pm' => ['quantity' => 3, 'pm_cost' => 1],
                        ],
                    ],
                    [
                        'key'       => 'pyra',
                        'name'      => 'Herança de Pyra',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'reroll_condition_checks_pm_cost' => 2,
                        ],
                    ],
                    [
                        'key'       => 'ramknal',
                        'name'      => 'Herança de Ramknal',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'skill_burst_bonus_options' => [
                                'choices' => ['Acrobacia', 'Enganação', 'Furtividade', 'Jogatina', 'Ladinagem'],
                                'quantity' => 2,
                                'pm_cost' => 2,
                                'bonus' => 5
                            ],
                        ],
                    ],
                    [
                        'key'       => 'serena',
                        'name'      => 'Herança de Serena',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'defense_bonus_unprovoked' => 2,
                            'saves_bonus_unprovoked'   => 2,
                            'exceptions'               => ['enfeitiçado', 'fascinado', 'pasmo'],
                        ],
                    ],
                    [
                        'key'       => 'skerry',
                        'name'      => 'Herança de Skerry',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'craft_pm_advantage' => ['pm_cost' => 1, 'effect' => 'trained_or_advantage'],
                        ],
                    ],
                    [
                        'key'       => 'solaris',
                        'name'      => 'Herança de Solaris',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'daytime_skill_bonus' => 1,
                            'direct_sun_extra'    => 1,
                        ],
                    ],
                    [
                        'key'       => 'sombria',
                        'name'      => 'Herança de Sombria',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'night_skill_bonus'   => 1,
                            'total_darkness_extra' => 1,
                        ],
                    ],
                    [
                        'key'       => 'sora',
                        'name'      => 'Herança de Sora',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'skill_bonuses' => [['skill' => 'Nobreza', 'bonus' => 2]],
                            'saving_throws' => ['will' => ['bonus' => 2]],
                            'extended_tasks_bonus' => 2,
                        ],
                    ],
                    [
                        'key'       => 'terapolis',
                        'name'      => 'Herança de Terápolis',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'skill_bonuses'   => [['skill' => 'Intuição', 'bonus' => 2]],
                            'saving_throws'   => ['will' => ['bonus' => 2]],
                            'auto_check_illusions' => true,
                        ],
                    ],
                    [
                        'key'       => 'venomia',
                        'name'      => 'Herança de Venomia',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'skill_bonuses' => [['skill' => 'Enganação', 'bonus' => 2]],
                            'combat_maneuvers_defense_bonus' => 2,
                            'movement_effects_defense_bonus' => 2,
                        ],
                    ],
                    [
                        'key'       => 'vitalia',
                        'name'      => 'Herança de Vitalia',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'hp_per_tier_bonus'              => 5,
                            'rest_healing_category_increase' => 1,
                        ],
                    ],
                    [
                        'key'       => 'werra',
                        'name'      => 'Herança de Werra',
                        'replaces'  => ['luz_sagrada', 'sombras_profanas'],
                        'effects'   => [
                            'attack_bonus_weapons' => 1,
                            'proficiencies_choice' => ['martial' => true, 'exotic' => ['count' => 2]],
                        ],
                    ],
                ],
            ]),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        /* ======================
            Suraggel — Aggelus
            (SAB +2, CAR +1)
        ====================== */
        $insertVariant(
            $suraggelId,
            [
                'key'     => 'aggelus',
                'name'    => 'Suraggel (Aggelus)',
                'summary' => 'Aspecto celeste: luz sagrada, tato social e intuição.',
                'meta'    => [
                    'core_feature_key' => 'luz_sagrada',
                    'skill_bonuses'    => [
                        ['skill' => 'Diplomacia', 'bonus' => 2],
                        ['skill' => 'Intuição',   'bonus' => 2],
                    ],
                    'magic' => [
                        'granted_spells' => [[
                            'name'   => 'Luz',
                            'circle' => 1,
                            'key'    => 'CAR',
                            'school' => 'divina'
                        ]],
                        'relearn_cost_reduction_pm' => 1
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'SAB', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'CAR', 'modifier' => +1],
            ]
        );

        /* ======================
            Suraggel — Sulfure
            (DES +2, INT +1)
        ====================== */
        $insertVariant(
            $suraggelId,
            [
                'key'     => 'sulfure',
                'name'    => 'Suraggel (Sulfure)',
                'summary' => 'Aspecto sombrio: sombras profanas, astúcia e furtividade.',
                'meta'    => [
                    'core_feature_key' => 'sombras_profanas',
                    'skill_bonuses'    => [
                        ['skill' => 'Enganação',  'bonus' => 2],
                        ['skill' => 'Furtividade', 'bonus' => 2],
                    ],
                    'magic' => [
                        'granted_spells' => [[
                            'name'   => 'Escuridão',
                            'circle' => 1,
                            'key'    => 'INT',
                            'school' => 'divina'
                        ]],
                        'relearn_cost_reduction_pm' => 1
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => +1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Trog',
                'slug'          => 'trog',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => 'CON +2, FOR +1, INT -1. Mau Cheiro (padrão, 2 PM; Fort CD CON; enjoado 1d6; imune 1 dia se passar); Mordida (arma natural; ataque extra 1 PM); Reptiliano (+1 DEF, visão no escuro, +5 Furtividade sem armadura/roupas pesadas); Sangue Frio (+1 por dado de frio).',
                'meta'          => [
                    'tags' => ['reptiliano'],
                    'features' => [
                        [
                            'key'   => 'mau_cheiro',
                            'name'  => 'Mau Cheiro',
                            'active' => [
                                'action'  => 'padrão',
                                'cost_pm' => 2,
                                'area'    => ['range' => 'curto', 'affects' => 'creatures', 'exclude_races' => ['Trog', 'Trog (Anão)']],
                                'save'    => ['type' => 'fortitude', 'dc' => ['attribute' => 'CON'], 'tag' => 'veneno'],
                                'on_fail' => [
                                    ['type' => 'condition', 'name' => 'enjoado', 'duration' => '1d6_rounds'],
                                ],
                                'on_success' => [
                                    ['type' => 'immunity', 'against' => 'mau_cheiro', 'duration' => '1_day'],
                                ],
                            ],
                        ],
                        [
                            'key'   => 'mordida',
                            'name'  => 'Mordida',
                            'natural_weapons' => [
                                [
                                    'id'     => 'bite',
                                    'label'  => 'Mordida',
                                    'damage' => ['formula' => '1d6', 'crit' => 'x2', 'type' => 'perfuração'],
                                ],
                            ],
                            'active' => [
                                'trigger'            => 'on_agredir_with_other_weapon',
                                'cost_pm'            => 1,
                                'grant_extra_attack' => ['weapon' => 'bite', 'range' => 'melee'],
                                'limit'              => 'once_per_round',
                            ],
                        ],
                        [
                            'key'   => 'reptiliano',
                            'name'  => 'Reptiliano',
                            'type_override' => 'monstro',
                            'senses'        => ['darkvision' => true],
                            'defense_bonus' => 1,
                            'skill_bonuses' => [
                                ['skill' => 'Furtividade', 'value' => 5, 'condition' => 'sem_armadura_ou_roupas_pesadas'],
                            ],
                        ],
                        [
                            'key'   => 'sangue_frio',
                            'name'  => 'Sangue Frio',
                            'vulnerability' => [
                                ['type' => 'cold', 'per_die_bonus' => 1],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CON', 'modifier' => +2],
                ['attribute' => 'FOR', 'modifier' => +1],
                ['attribute' => 'INT', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Trog (Anão)',
                'slug'          => 'trog-anao',
                'size'          => 'Médio',
                'speed'         => 6,
                'creature_type' => 'monstro',
                'source'        => 'T20 (AA)',
                'summary'       => 'Variante anã do Trog. DES −1. Substitui Reptiliano por Quase Anão (visão no escuro, +1 PV/nível, desloc. 6m não reduzido por armadura/carga). Mantém Mau Cheiro, Mordida e Sangue Frio.',
                'meta'          => [
                    'variant_of' => 'trog',
                    'features' => [
                        [
                            'key'   => 'mau_cheiro',
                            'name'  => 'Mau Cheiro',
                            'active' => [
                                'action'  => 'padrão',
                                'cost_pm' => 2,
                                'area'    => ['range' => 'curto', 'affects' => 'creatures', 'exclude_races' => ['Trog', 'Trog (Anão)']],
                                'save'    => ['type' => 'fortitude', 'dc' => ['attribute' => 'CON'], 'tag' => 'veneno'],
                                'on_fail' => [
                                    ['type' => 'condition', 'name' => 'enjoado', 'duration' => '1d6_rounds'],
                                ],
                                'on_success' => [
                                    ['type' => 'immunity', 'against' => 'mau_cheiro', 'duration' => '1_day'],
                                ],
                            ],
                        ],
                        [
                            'key'   => 'mordida',
                            'name'  => 'Mordida',
                            'natural_weapons' => [
                                [
                                    'id'     => 'bite',
                                    'label'  => 'Mordida',
                                    'damage' => ['formula' => '1d6', 'crit' => 'x2', 'type' => 'perfuração'],
                                ],
                            ],
                            'active' => [
                                'trigger'            => 'on_agredir_with_other_weapon',
                                'cost_pm'            => 1,
                                'grant_extra_attack' => ['weapon' => 'bite', 'range' => 'melee'],
                                'limit'              => 'once_per_round',
                            ],
                        ],
                        [
                            'key'   => 'quase_anao',
                            'name'  => 'Quase Anão',
                            'replaces' => 'reptiliano',
                            'type_override' => 'monstro',
                            'senses'        => ['darkvision' => true],
                            'hp_per_level_bonus' => 1,
                            'speed_rules' => [
                                'base'  => 6,
                                'never_reduced_by_armor_or_load' => true,
                            ],
                        ],
                        [
                            'key'   => 'sangue_frio',
                            'name'  => 'Sangue Frio',
                            'vulnerability' => [
                                ['type' => 'cold', 'per_die_bonus' => 1],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CON', 'modifier' => +2],
                ['attribute' => 'FOR', 'modifier' => +1],
                ['attribute' => 'INT', 'modifier' => -1],
                ['attribute' => 'DES', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Bugbear',
                'slug'          => 'bugbear',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => 'FOR +2, DES +1, CAR −1. Empunhadura Poderosa (melhor lida com armas maiores). Saborear Pavor (usa FOR em Intimidação; bônus de ataque perto de inimigos abalados/apavorados). Sentidos de Predador (faro e visão no escuro).',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'empunhadura_poderosa',
                            'name'  => 'Empunhadura Poderosa',
                            // Manejo de arma de tamanho maior
                            'oversized_weapon_handling' => [
                                // padrão desta raça: usar arma de 1 categoria maior com penalidade reduzida para −2
                                'one_size_larger_attack_penalty' => -2,
                                // se a habilidade for recebida novamente (por talento/poder), melhora:
                                'stacking_upgrade' => [
                                    'one_size_larger_attack_penalty' => 0,
                                    'two_sizes_larger_attack_penalty' => -5,
                                ],
                            ],
                        ],
                        [
                            'key'   => 'saborear_pavor',
                            'name'  => 'Saborear Pavor',
                            'skill_key_attribute_override' => [
                                // Intimidação usa FOR em vez de CAR
                                ['skill' => 'Intimidação', 'attribute' => 'FOR'],
                            ],
                            'conditional_attack_bonus' => [
                                'range'     => 'curto',
                                'trigger'   => ['enemy_conditions' => ['abalado', 'apavorado']],
                                // bônus de ataque = penalidade da condição do alvo
                                'bonus_equals_condition_penalty' => true,
                            ],
                        ],
                        [
                            'key'   => 'sentidos_de_predador',
                            'name'  => 'Sentidos de Predador',
                            'senses' => [
                                'darkvision' => true,
                                'scent'      => true,
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'FOR', 'modifier' => +2],
                ['attribute' => 'DES', 'modifier' => +1],
                ['attribute' => 'CAR', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Centauro',
                'slug'          => 'centauro',
                'size'          => 'Grande',
                'speed'         => 12,
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => 'SAB +2, FOR +1, DES -1, INT -1. Avantajado (Grande, 12m). Cascos (1d8; ataque extra 1 PM). Ginete Natural (conta como montado p/ investida e benefícios de armas; pode pegar Carga de Cavalaria; não se beneficia de montaria; carregando cavaleiro sofre −2 em testes e fica em condição ruim p/ conjurar). Medo de Altura (abalado junto a quedas ≥3m).',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'avantajado',
                            'name'  => 'Avantajado',
                            'size_override' => 'Grande',
                            'speeds' => ['land' => 12],
                        ],
                        [
                            'key'   => 'cascos',
                            'name'  => 'Cascos',
                            'natural_weapons' => [
                                [
                                    'id'     => 'hooves',
                                    'label'  => 'Cascos',
                                    'damage' => ['formula' => '1d8', 'crit' => 'x2', 'type' => 'impacto'],
                                ],
                            ],
                            'active' => [
                                'trigger'            => 'on_agredir_with_other_weapon',
                                'cost_pm'            => 1,
                                'grant_extra_attack' => ['weapon' => 'hooves', 'range' => 'melee'],
                                'limit'              => 'once_per_round',
                            ],
                        ],
                        [
                            'key'   => 'ginete_natural',
                            'name'  => 'Ginete Natural',
                            'mounted_interactions' => [
                                'counts_as_mounted_for' => ['investidas', 'beneficios_de_armas'],
                                'grants_feat_access'    => ['carga_de_cavalaria'],
                                'cannot_benefit_from_mount' => true,
                                'when_carrying_rider' => [
                                    'tests_penalty' => -2,
                                    'casting_condition' => 'condicao_ruim',
                                    'notes' => 'Penalidades de sobrecarga ainda se aplicam, se houver.',
                                ],
                            ],
                        ],
                        [
                            'key'   => 'medo_de_altura',
                            'name'  => 'Medo de Altura',
                            'fear_trigger' => [
                                'adjacent_to_drop_m_or_more' => 3,
                                'condition' => 'abalado',
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'SAB', 'modifier' => +2],
                ['attribute' => 'FOR', 'modifier' => +1],
                ['attribute' => 'DES', 'modifier' => -1],
                ['attribute' => 'INT', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Ceratops',
                'slug'          => 'ceratops',
                'size'          => 'Grande',
                'speed'         => 9,
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => 'CON +2, FOR +1, DES -1, INT -1. Chifres (1d8 perf.; ataque extra 1 PM). Paquidérmico (Grande, +1 DEF; Intimidação usa FOR). Papel Tribal (treinado em Cura/Intimidação/Ofício/Sobrevivência). Medo de Altura (abalado junto a quedas ≥3m).',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'chifres',
                            'name'  => 'Chifres',
                            'natural_weapons' => [
                                [
                                    'id'     => 'horns',
                                    'label'  => 'Chifres',
                                    'damage' => ['formula' => '1d8', 'crit' => 'x2', 'type' => 'perfuração'],
                                ],
                            ],
                            'active' => [
                                'trigger'            => 'on_agredir_with_other_weapon',
                                'cost_pm'            => 1,
                                'grant_extra_attack' => ['weapon' => 'horns', 'range' => 'melee'],
                                'limit'              => 'once_per_round',
                            ],
                        ],
                        [
                            'key'   => 'paquidemico',
                            'name'  => 'Paquidérmico',
                            'size_override' => 'Grande',
                            'defense_bonus'  => 1,
                            'skill_key_attribute_override' => [
                                ['skill' => 'Intimidação', 'attribute' => 'FOR'],
                            ],
                        ],
                        [
                            'key'   => 'papel_tribal',
                            'name'  => 'Papel Tribal',
                            'grants_training_choice' => [
                                'skills' => ['Cura', 'Intimidação', 'Ofício', 'Sobrevivência'],
                                'count'  => 1,
                            ],
                        ],
                        [
                            'key'   => 'medo_de_altura',
                            'name'  => 'Medo de Altura',
                            'fear_trigger' => [
                                'adjacent_to_drop_m_or_more' => 3,
                                'condition' => 'abalado',
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'CON', 'modifier' => +2],
                ['attribute' => 'FOR', 'modifier' => +1],
                ['attribute' => 'DES', 'modifier' => -1],
                ['attribute' => 'INT', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Elfo-do-Mar',
                'slug'          => 'elfo-do-mar',
                'size'          => 'Médio',
                'speed'         => 9, // terrestre
                'creature_type' => 'humanoide',
                'source'        => 'T20',
                'summary'       => 'DES +2, CON +1, INT −1. Arsenal do Oceano (proficiência: arpão, rede, tridente; +2 ataque; se ganhar prof. de novo, conta como arma leve). Cria das Águas (natação = terrestre; visão na penumbra; na água: percepção às cegas 18m, +2 Defesa, +2 Furtividade e Sobrevivência). Dependência de Água (sem água >1 dia: não recupera PM até ter contato).',
                'meta'          => [
                    'features' => [
                        [
                            'key'   => 'arsenal_do_oceano',
                            'name'  => 'Arsenal do Oceano',
                            'proficiencies' => ['Arpão', 'Rede', 'Tridente'],
                            'attack_bonuses' => [
                                ['weapon' => 'Arpão',  'value' => 2],
                                ['weapon' => 'Rede',   'value' => 2],
                                ['weapon' => 'Tridente', 'value' => 2],
                            ],
                            // Se receber proficiência novamente por outra fonte
                            'stacking_upgrade' => [
                                ['weapon' => 'Arpão',   'treat_as_light' => true],
                                ['weapon' => 'Rede',    'treat_as_light' => true],
                                ['weapon' => 'Tridente', 'treat_as_light' => true],
                            ],
                        ],
                        [
                            'key'   => 'cria_das_aguas',
                            'name'  => 'Cria das Águas',
                            'speeds' => [
                                'land'  => 9,
                                'swim'  => 'equal_to_land', // natação = terrestre
                            ],
                            'senses' => [
                                'low_light_vision' => true, // visão na penumbra
                            ],
                            'while_in_water' => [
                                'blindsense'    => 18, // metros
                                'defense_bonus' => 2,
                                'skill_bonuses' => [
                                    ['skill' => 'Furtividade',    'value' => 2],
                                    ['skill' => 'Sobrevivência',  'value' => 2],
                                ],
                            ],
                        ],
                        [
                            'key'   => 'dependencia_de_agua',
                            'name'  => 'Dependência de Água',
                            'rest_requirements' => [
                                'water_contact_interval_days' => 1,
                                'if_unmet' => [
                                    'no_mana_recovery_on_rest' => true,
                                    'note' => 'Recupera PM normalmente após voltar a ter contato com água.',
                                ],
                            ],
                        ],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'DES', 'modifier' => +2],
                ['attribute' => 'CON', 'modifier' => +1],
                ['attribute' => 'INT', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Finntroll',
                'slug'          => 'finntroll',
                'size'          => 'Médio',
                'speed'         => 9,
                'creature_type' => 'monstro',
                'source'        => 'T20',
                'summary'       => 'INT +2, CON +1, FOR -1. Corpo vegetal, presença arcana e regeneração (bloqueada por luz direta).',
                'meta'          => [
                    'tags' => ['vegetal'],
                    // Corpo Vegetal / Natureza Vegetal
                    'senses' => ['darkvision' => true],
                    'immunities' => ['atordoamento', 'metamorfose'],
                    'plant_interactions' => [
                        // é afetado por efeitos “vs. plantas” e ganha TR de Fortitude contra magias sem TR
                        'affected_by_plant_specific_effects' => true,
                        'fort_save_for_no_save_spells' => true,
                    ],

                    // Presença Arcana
                    'skill_bonuses' => [
                        ['skill' => 'Misticismo', 'bonus' => 2],
                    ],
                    'magic_resistance_bonus' => 2,

                    // Regeneração Vegetal + Intolerância à Luz
                    'regeneration' => [
                        'name'            => 'Regeneração Vegetal',
                        'cost_pm'         => 1,
                        'frequency'       => 'once_per_round',
                        'heal_amount'     => 5,               // PV por ativação
                        'blocked_by'      => ['ácido', 'fogo'],
                        'requires'        => ['not_in_direct_sunlight' => true],
                    ],
                    'light_sensitivity' => [
                        // sob sol/luz similar, a regeneração não pode ser ativada
                        'sunlight_blocks' => ['regeneration'],
                    ],
                ],
                'is_official'   => true,
                'created_by_id' => null,
            ],
            [
                ['attribute' => 'INT', 'modifier' => +2],
                ['attribute' => 'CON', 'modifier' => +1],
                ['attribute' => 'FOR', 'modifier' => -1],
            ]
        );

        $insertRace(
            [
                'name'          => 'Harpia',
                'slug'          => 'harpia',
                'creature_type' => 'monstro',
                'size'          => 'Médio',
                'summary'       => 'Predadoras aladas com asas no lugar dos braços e garras nos pés.',
                'meta'          => [
                    'tags' => ['voo', 'monstro', 'feminino', 'grito'],
                    'movement' => [
                        // Pairar a 1,5m com 12m: ignora terreno difícil e imune a dano de queda (se consciente)
                        'hover' => 12,
                        'ignore_difficult_terrain_while_hover' => true,
                        'fall_damage_immunity_when_conscious' => true,
                        // Voo pagando 1 PM/rodada, se não estiver com armadura pesada
                        'fly' => ['speed' => 18, 'pm_per_round' => 1, 'requires_no_heavy_armor' => true],
                    ],
                    'vision' => ['darkvision' => true],
                    'skill_bonuses' => ['Intimidação' => 2, 'Sobrevivência' => 2],
                    'natural_weapons' => [
                        ['name' => 'garras (pés)', 'dice' => '1d6', 'crit' => 'x2', 'type' => 'corte', 'count' => 2],
                    ],
                    'notes' => [
                        'Asas de Abutre: braços são asas; paira a 1,5m (12m); 1 PM/rodada para voar (18m) sem armadura pesada.',
                        'Cria de Masmorra: tipo monstro, visão no escuro, +2 em Intimidação e Sobrevivência.',
                        'Grito Aterrorizante: ação padrão, 1 PM, alcance curto; Vontade CD CAR evita; abalado se falhar.',
                        'Pés Rapinantes: pés funcionam como mãos ou 2 garras (1d6, corte). 1x/rodada, ao agredir com outra arma, 1 PM para ataque extra com a garra livre; pode contar como arma secundária para estilos de duas armas.',
                        'Apenas mulheres.',
                    ],
                ],
            ],
            [
                ['attribute' => 'DES', 'modifier' => +2],
                ['attribute' => 'CAR', 'modifier' => +1],
                ['attribute' => 'INT', 'modifier' => -1],
            ]
        );

        // Kaijin — FOR +2, CON +1, CAR -2
        $insertRace(
            [
                'name'          => 'Kaijin',
                'slug'          => 'kaijin',
                'creature_type' => 'monstro',
                'size'          => 'Médio',
                'summary'       => 'Mutantes da Tormenta com couraça rubra e presença aterradora.',
                'meta'          => [
                    'tags' => ['tormenta', 'monstro', 'couraça', 'disforme'],
                    // Couraça Rubra
                    'defenses' => [
                        'damage_reduction' => ['value' => 2, 'type' => 'generic']
                    ],
                    // Cria da Tormenta
                    'tormenta_child' => [
                        'save_bonus_vs_tormenta' => 5,
                        'immune_effects_not_affecting_lefou' => true
                    ],
                    // Disforme: itens precisam ser adaptados
                    'equipment' => [
                        'requires_adaptation' => true,
                        'adapt_time_days' => 1,
                        'adapt_cost_ratio' => 0.5,
                        'origin_items_already_adapted' => true
                    ],
                    // Terror Vivo
                    'skill_substitutions' => ['Intimidação' => 'FOR'],
                    'grants' => [
                        'poder_tormenta_free' => 1,
                        'poderes_tormenta_no_cha_loss' => true
                    ],
                    'notes' => [
                        'Couraça Rubra: RD 2.',
                        'Cria da Tormenta: +5 em testes contra efeitos de lefeu/Tormenta; imune a efeitos da Tormenta que não afetem lefou.',
                        'Disforme: não usa itens mundanos sem adaptação (1 dia, 50% do valor); itens de origem/habilidades já adaptados.',
                        'Terror Vivo: pode usar FOR como atributo-chave de Intimidação; ganha 1 poder da Tormenta que não conta para perda de CAR.'
                    ],
                ],
            ],
            [
                ['attribute' => 'FOR', 'modifier' => +2],
                ['attribute' => 'CON', 'modifier' => +1],
                ['attribute' => 'CAR', 'modifier' => -2],
            ]
        );

        // Kallyanach — +2 em um atributo OU +1 em dois atributos (modelado como 2 escolhas de +1: repetir => +2 no mesmo)
        $insertRace(
            [
                'name'          => 'Kallyanach',
                'slug'          => 'kallyanach',
                'creature_type' => 'monstro',
                'size'          => 'Médio',
                'summary'       => 'Descendentes tocados por Kallyadranoch; herança dracônica e bênçãos escolhidas.',
                'meta'          => [
                    'tags' => ['tormenta', 'monstro', 'dracônico'],

                    // Herança Dracônica: redução 5 a um tipo (escolha)
                    'draconic_heritage' => [
                        'element_types' => ['ácido', 'eletricidade', 'fogo', 'frio', 'luz', 'trevas'],
                        'damage_reduction' => [
                            'value' => 5,
                            'upgrades_to' => 10,            // se tiver 'escamas_elementais'
                            'upgrade_key' => 'escamas_elementais'
                        ],
                    ],

                    // BÊNÇÃOS (cada uma é opcional; escolha 2 no total)
                    'blessings' => [
                        'armamento_kallyanach' => [
                            'label' => 'Armamento Kallyanach',
                            'natural_weapon' => [
                                'base_damage' => '1d6',
                                'crit' => 'x2',
                                'choices' => [
                                    ['name' => 'cauda',   'damage_type' => 'impacto'],
                                    ['name' => 'chifres', 'damage_type' => 'perfuração'],
                                    ['name' => 'mordida', 'damage_type' => 'corte'],
                                ],
                                'extra_attack' => [
                                    'pm_cost' => 1,
                                    'once_per_round' => true,
                                    'condition' => 'após agredir com outra arma',
                                ],
                            ],
                        ],
                        'asas_draconicas' => [
                            'label' => 'Asas Dracônicas',
                            'fly' => [
                                'pm_cost_per_round' => 1,
                                'speed_m' => 9,
                                'vulnerable_while_flying' => true,
                            ],
                        ],
                        'escamas_elementais' => [
                            'label' => 'Escamas Elementais',
                            'defense_bonus' => 2,
                            'heritage_dr_upgrade_to' => 10,
                        ],
                        'pratica_arcana' => [
                            'label' => 'Prática Arcana',
                            'circle' => 1,
                            'school' => 'arcana',
                            'attribute_key' => 'INT',
                            'damage_type_must_match_heritage' => true,
                            'can_take_multiple_for_other_spells' => true,
                            'reduces_cost_if_relearned' => true,
                        ],
                        'sentidos_draconicos' => [
                            'label' => 'Sentidos Dracônicos',
                            'senses' => ['faro', 'visão no escuro'],
                        ],
                        'sopro_de_dragao' => [
                            'label' => 'Sopro de Dragão',
                            'shape' => 'cone',
                            'range_m' => 6,
                            'pm_cost' => 1,
                            'damage' => '1d12',
                            'damage_type' => 'igual à Herança Dracônica',
                            'save' => ['type' => 'Reflexos', 'dc_attribute' => 'CON', 'effect_on_success' => 'metade'],
                            'scaling' => [
                                'per_levels' => 4,
                                'extra_pm_cost' => 1,
                                'extra_damage' => '+1d12',
                            ],
                        ],
                        'feiticeiro_draconico_synergy' => [
                            'label' => 'Kallyanach Feiticeiros Dracônicos',
                            'effects' => [
                                'breath_attribute_override' => 'CAR',
                                'practice_magic_attr_override' => 'CAR',
                                'breath_counts_as_sorcerer_spell' => true,
                            ],
                        ],
                    ],
                    'blessings_pick' => 2, // o jogador escolhe 2 bênçãos
                    'notes' => [
                        'Aumentos de atributo: +2 em um atributo OU +1 em dois atributos.',
                        'A RD da Herança Dracônica pode subir para 10 com Escamas Elementais.',
                    ],
                ],
            ],
            [
                // Modelagem do bônus flexível: 2 escolhas de +1 (permitir repetir => +2 no mesmo atributo).
                [
                    'mode' => 'choice',
                    'modifier' => +1,
                    'quantity' => 2,
                    'notes' => 'Pode distribuir +1/+1 em atributos diferentes ou repetir para +2 no mesmo atributo.'
                ],
            ]
        );

        // Kappa — DES +2, CON +1, CAR –1
        $insertRace(
            [
                'name'          => 'Kappa',
                'slug'          => 'kappa',
                'creature_type' => 'espírito',
                'size'          => 'Médio',
                'summary'       => 'Espírito aquático com carapaça, tigela d’água e dons de cura.',
                'meta'          => [
                    'tags' => ['tormenta', 'espírito', 'aquático'],

                    // Alma da Água: espírito + natação = desloc. terrestre
                    'alma_da_agua' => [
                        'is_spirit' => true,
                        'swim_speed_equals_land' => true,
                    ],

                    // Carapaça Kappa: anti-flanqueado, cobertura leve submerso/caído,
                    // soma CON na Defesa (limitado pelo nível) se não usar armadura pesada.
                    // Se já tiver "soma CON na Defesa" de outra fonte, recebe +2 Defesa em vez disso.
                    'carapaca_kappa' => [
                        'cannot_be_flanked' => true,
                        'light_cover_when' => ['submerso', 'caido'],
                        'defense' => [
                            'add_attribute' => 'CON',
                            'cap_by_level' => true,
                            'only_if' => ['no_heavy_armor' => true],
                            'replacement_if_already_has_con_to_defense' => ['flat_bonus' => 2],
                        ],
                    ],

                    // Cura das Águas: Curar Ferimentos (chave SAB), -1 PM se reaprender,
                    // bloqueada se a água da tigela estiver derramada.
                    'cura_das_aguas' => [
                        'spell' => [
                            'name' => 'Curar Ferimentos',
                            'circle' => 1,
                            'school' => 'divina',
                            'attribute_key' => 'SAB',
                            'reduces_cost_if_relearned' => true,
                        ],
                        'requires_head_water' => true,
                    ],

                    // Tigela d’Água: se falhar por 5+ em agarrar/derrubar/empurrar, derrama;
                    // fica enjoadx até encher (ação padrão + fonte de água).
                    'tigela_dagua' => [
                        'spill_triggers' => ['grapple_fail_by_5', 'trip_fail_by_5', 'push_fail_by_5'],
                        'condition_on_spill' => 'enjoado',
                        'refill' => ['needs_water_source' => true, 'action' => 'padrão'],
                    ],
                ],
            ],
            [
                ['attribute' => 'DES', 'modifier' => +2],
                ['attribute' => 'CON', 'modifier' => +1],
                ['attribute' => 'CAR', 'modifier' => -1],
            ]
        );

        // Kobolds — DES +2, FOR –1
        $insertRace(
            [
                'name'          => 'Kobolds',
                'slug'          => 'kobolds',
                'size'          => 'Médio',          // contam como criatura Média (ver regras especiais abaixo)
                'creature_type' => 'monstro',
                'summary'       => 'Enxame escamoso de kobolds que age como uma única criatura. Monstruosos, adaptáveis e astutos.',
                'meta'          => [
                    'tags' => ['tormenta', 'monstro', 'enxame'],

                    // Enxame Escamoso
                    'enxame_escamoso' => [
                        'arms' => 2,                          // “uma única criatura Média com dois braços”
                        'counts_as_small_for_passage' => true, // conta como Pequeno para espaços apertados
                        'single_target_save_advantage' => true, // em testes de resistência vs efeitos que afetam 1 alvo e não causam dano: rola 2d e usa o melhor
                        'area_damage_vulnerability' => true,   // vulnerabilidade a dano de área
                    ],

                    // Praga Monstruosa
                    'praga_monstruosa' => [
                        'darkvision' => true,
                        'skill_bonuses' => [
                            ['skill' => 'Sobrevivência', 'bonus' => 2],
                        ],
                    ],

                    // Sensibilidade à Luz
                    'sensibilidade_luz' => [
                        'dazzled_in_sunlight' => true, // ofuscado sob luz solar ou similar
                    ],

                    // Talentos do Bando — escolha 2 ao criar (e 1 por patamar em troca de poder de classe)
                    'talentos_do_bando' => [
                        'choose' => 2,
                        'options' => [
                            [
                                'key'   => 'amontoados',
                                'label' => 'Amontoados',
                                'effect' => [
                                    'counts_as_large_for_space_and_cmb' => true, // conta como Grande p/ espaço/mod. manobras
                                    'configurable_formation' => [
                                        'cubes' => '4x 1.5m', // quatro cubos 1,5m; pode reconfigurar com ação de movimento
                                        'reconfigure_on_move' => true,
                                        'scales_with_size_increase' => true, // acumula com efeitos de aumento de tamanho
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'armadilha_terrivel',
                                'label' => 'Armadilha Terrível',
                                'effect' => [
                                    'portable_trap' => [
                                        'spell_circle' => 1,
                                        'target' => 'criatura_ou_area',
                                        'causes' => 'dano_ou_efeito_negativo',
                                        'as_engineering' => true, // usa regras de engenhocas
                                        'skill' => 'Sobrevivência',
                                        'attribute_key' => 'SAB',
                                        'stackable' => true, // pode escolher mais de uma para magias diferentes
                                    ]
                                ],
                            ],
                            [
                                'key'   => 'diferentao',
                                'label' => 'Diferentão',
                                'effect' => [
                                    'off_class_power' => [
                                        'level_treated_as' => 'character_level_minus_4',
                                        'requires_prereqs' => true,
                                    ]
                                ],
                            ],
                            [
                                'key'   => 'ex_familiar',
                                'label' => 'Ex-Familiar',
                                'effect' => [
                                    'pm_bonus' => 2,
                                    'familiar' => [
                                        'type' => 'arcanista_basico',
                                        'attribute_fallback' => 'CAR', // se não tiver atributo-chave de conjuração
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'o_ousado',
                                'label' => 'O Ousado',
                                'effect' => [
                                    'spawn_runner' => [
                                        'uses'    => '1_por_cena',
                                        'cost_pm' => 1,
                                        'action'  => 'movimento',
                                        'acts_next_round' => true,
                                        'size'    => 'Pequeno',
                                        'hp'      => 1,
                                        'speed'   => 9,
                                        'attack'  => [
                                            'action' => 'padrao',
                                            'damage' => '2d4 corte', // escala 1 passo por patamar além de iniciante
                                            'scales_per_tier' => true,
                                        ],
                                        'auto_fail_opposed_checks' => true,
                                        'returns_on_death_or_end_scene' => true,
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'os_do_fundo',
                                'label' => 'Os do Fundo',
                                'prerequisites' => ['organizadinhos'],
                                'effect' => [
                                    'third_arm_like' => [
                                        'can_hold_object' => true,
                                        'no_extra_actions' => true,
                                        'extra_attack_option' => [
                                            'once_per_round' => true,
                                            'cost_pm' => 1,
                                            'requires' => 'arma_leve',
                                            'when' => 'ao_agredir_com_outra_arma',
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'organizadinhos',
                                'label' => 'Organizadinhos',
                                'effect' => [
                                    'carry_limit_uses_dex' => true, // usa DES no lugar de FOR p/ carga
                                    'extra_worn_item_slot' => true,
                                ],
                            ],
                            [
                                'key'   => 'pestes_oportunistas',
                                'label' => 'Pestes Oportunistas',
                                'effect' => [
                                    'once_per_round_damage_bonus' => [
                                        'condition' => 'alvo_ja_ferido_na_mesma_rodada',
                                        'bonus'     => '1d6 mesmo_tipo',
                                        'scales_per_tier' => true, // aumenta 1 passo por patamar além de iniciante
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'somos_explosivos',
                                'label' => 'Somos Explosivos',
                                'effect' => [
                                    'kobold_bomb' => [
                                        'action'  => 'completa',
                                        'range'   => 'alcance_curto',
                                        'cost_hp' => 'ate_nivel', // gasta PV até o limite do nível
                                        'area'    => 'raio_3m',
                                        'damage'  => '1d6 impacto por PV',
                                        'save'    => ['type' => 'Reflexos', 'attribute' => 'DES', 'effect' => 'meio_dano'],
                                        'explode_6_additional_d6' => true, // em cada dado que der 6, +1d6
                                    ],
                                ],
                            ],
                            [
                                'key'   => 'tatica_de_enxame',
                                'label' => 'Tática de Enxame',
                                'prerequisites' => ['amontoados'],
                                'effect' => [
                                    'swarm_form' => [
                                        'cost_pm' => 2,
                                        'duration' => 'sustentada',
                                        'can_occupy_enemy_space' => true,
                                        'immune_to_combat_maneuvers' => true,
                                        'weapon_damage_taken' => 'metade',
                                        'restrictions' => ['no_coordination_actions' => true], // sem furtividade/conjuração etc.
                                        'casters_in_space_are_hampered' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                ['attribute' => 'DES', 'modifier' => +2],
                ['attribute' => 'FOR', 'modifier' => -1],
            ]
        );

        // Meio-Orc — FOR +2, +1 em outro atributo (exceto CAR)
        $insertRace(
            [
                'name'          => 'Meio-Orc',
                'slug'          => 'meio-orc',
                'size'          => 'Médio',
                'creature_type' => 'humanoide',
                'summary'       => 'Descendente de orcs, robusto e adaptável; exímio no subterrâneo e mais letal em combate corpo a corpo.',
                'meta'          => [
                    'tags' => ['tormenta', 'humanoide', 'orc'],

                    // Adaptável: +2 Intimidação e 1 perícia treinada à escolha
                    'adaptavel' => [
                        'skill_bonuses' => [
                            ['skill' => 'Intimidação', 'bonus' => 2],
                        ],
                        'extra_trained_skills' => 1,
                    ],

                    // Criatura das Profundezas: visão no escuro e +2 Percepção/Sobrevivência no subterrâneo
                    'criatura_das_profundezas' => [
                        'darkvision' => true,
                        'skill_bonuses' => [
                            ['skill' => 'Percepção',     'bonus' => 2, 'context' => 'subterraneo'],
                            ['skill' => 'Sobrevivência', 'bonus' => 2, 'context' => 'subterraneo'],
                        ],
                    ],

                    // Sangue Orc: +1 dano com armas corpo a corpo e de arremesso; conta como orc
                    'sangue_orc' => [
                        'damage_bonus' => [
                            ['category' => 'melee',  'bonus' => 1],
                            ['category' => 'thrown', 'bonus' => 1],
                        ],
                        'counts_as' => ['orc'],
                    ],
                ],
            ],
            [
                ['attribute' => 'FOR', 'modifier' => +2],
                // +1 em outro atributo à escolha, exceto CAR
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 1, 'exclusions' => ['CAR']],
            ]
        );

        // Minauro — FOR +1, +1 em dois atributos quaisquer
        $insertRace(
            [
                'name'          => 'Minauro',
                'slug'          => 'minauro',
                'size'          => 'Médio',
                'creature_type' => 'humanoide',
                'summary'       => 'Versátil e de faro apurado; aberto a novas ideias e treinamentos.',
                'meta'          => [
                    'tags' => ['tormenta', 'humanoide'],

                    // Faro: olfato apurado; não fica desprevenido contra inimigos não vistos em alcance curto;
                    // camuflagem total = 20% de chance de falha.
                    'faro' => [
                        'scent'                              => true,
                        'range'                              => 'curto',
                        'unseen_not_flat_footed'             => true,
                        'total_concealment_miss_chance_pct'  => 20,
                    ],

                    // Mente Aberta: +2 em Diplomacia e Investigação
                    'mente_aberta' => [
                        'skill_bonuses' => [
                            ['skill' => 'Diplomacia',    'bonus' => 2],
                            ['skill' => 'Investigação',  'bonus' => 2],
                        ],
                    ],

                    // Plurivalente: recebe 1 poder geral à escolha
                    'plurivalente' => [
                        'general_power_choices' => 1,
                    ],
                ],
            ],
            [
                ['attribute' => 'FOR', 'modifier' => +1],
                // +1 em dois atributos quaisquer (sem exclusões)
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
            ]
        );

        $moreauId = DB::table('races')->insertGetId([
            'slug'          => 'moreau',
            'name'          => 'Moreau',
            'size'          => 'Médio',
            'speed'         => 9,
            'creature_type' => 'humanoide', // base; algumas heranças ganham traits no meta
            'source'        => 'T20',
            'summary'       => 'Humanóides com traços animais de diversas heranças.',
            'meta'          => json_encode(['has_heritage' => true]),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        /* ======================
            Herança da Coruja
        ====================== */
        $insertVariant(
            $moreauId,
            [
                'key'   => 'coruja',
                'name'  => 'Herança da Coruja',
                'summary' => 'Espreitadores noturnos; sentidos aguçados e magia de adivinhação.',
                'meta'  => [
                    'senses' => ['darkvision' => true],
                    'skill_bonuses' => [
                        ['skill' => 'Percepção', 'bonus' => 2],
                        ['save'  => 'Vontade',   'bonus' => 2],
                    ],
                    'natural_weapons' => [
                        [
                            'type' => 'garras',
                            'count' => 2,
                            'damage' => '1d6',
                            'crit' => 'x2',
                            'damage_type' => 'corte',
                            'extra_attack_once_per_round_pm' => 1,
                            'can_be_offhand_for_styles' => true,
                        ],
                    ],
                    'innate_magic' => [
                        'school' => 'adivinhação',
                        'circle' => 1,
                        'key_ability' => 'SAB',
                        'relearn_discount' => 1, // -1 PM se aprender de novo
                    ],
                ],
            ],
            [
                ['attribute' => 'SAB', 'modifier' => +1],
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
            ]
        );

        /* ======================
            Herança da Hiena
        ====================== */
        $insertVariant(
            $moreauId,
            [
                'key'   => 'hiena',
                'name'  => 'Herança da Hiena',
                'summary' => 'Destemidos caçadores oportunistas, guiados pelo faro.',
                'meta'  => [
                    'destemor' => [
                        'vs_creatures_larger' => true,
                        'damage_bonus'        => 2,
                        'save_bonus'          => 2,
                    ],
                    'scent'  => ['range' => 'curto', 'total_concealment_miss_chance_pct' => 20, 'not_flat_footed_vs_unseen' => true],
                    'natural_weapons' => [
                        [
                            'type' => 'mordida',
                            'damage' => '1d6',
                            'crit' => 'x2',
                            'damage_type' => 'perfuração',
                            'extra_attack_once_per_round_pm' => 1,
                        ],
                    ],
                ],
            ],
            [
                ['attribute' => 'SAB', 'modifier' => +1],
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
            ]
        );

        /* ======================
            Herança da Raposa
        ====================== */
        $insertVariant(
            $moreauId,
            [
                'key'   => 'raposa',
                'name'  => 'Herança da Raposa',
                'summary' => 'Ágeis e astutos; peritos sociais e velozes.',
                'meta'  => [
                    'movement' => ['land' => 12],
                    'senses'   => ['low_light_vision' => true],
                    'scent'    => ['range' => 'curto', 'total_concealment_miss_chance_pct' => 20, 'not_flat_footed_vs_unseen' => true],
                    'flex_skill_bonuses' => [
                        'count' => 2,
                        'stat_allowed' => ['INT', 'CAR'], // jogador escolherá duas perícias baseadas em INT/CAR para +2
                        'bonus' => 2,
                    ],
                ],
            ],
            [
                ['attribute' => 'INT', 'modifier' => +1],
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
            ]
        );

        /* ======================
            Herança da Serpente
        ====================== */
        $insertVariant(
            $moreauId,
            [
                'key'   => 'serpente',
                'name'  => 'Herança da Serpente',
                'summary' => 'Arborícolas sorrateiros, dominam o agarrar e tramas mentais.',
                'meta'  => [
                    'movement' => ['climb' => 6],
                    'skill_bonuses' => [
                        ['skill' => 'Furtividade', 'bonus' => 2],
                        ['skill' => 'Diplomacia',  'bonus' => 2],
                    ],
                    'grapple' => ['bonus' => 2, 'extra_damage_vs_grappled' => 2],
                    'senses'  => ['darkvision' => true],
                    'mental_effects_dc_bonus' => 2,
                ],
            ],
            [
                ['attribute' => 'INT', 'modifier' => +1],
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
            ]
        );

        /* ======================
            Herança do Búfalo
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'bufalo',
            'name'    => 'Herança do Búfalo',
            'summary' => 'Chifres, faro e investidas brutais.',
            'meta'    => [
                'natural_weapons' => [
                    ['type' => 'horns', 'damage' => '1d6', 'crit' => 'x2', 'kind' => 'piercing', 'extra_attack_pm' => 1],
                ],
                'senses' => ['scent' => true],
                'combat_mods' => [
                    'charge_attack_bonus' => 2,
                    'bull_rush_bonus'     => 2,
                    'intimidation_uses'   => ['attribute_override' => 'FOR'],
                ],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'FOR', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        /* ======================
            Herança do Coelho
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'coelho',
            'name'    => 'Herança do Coelho',
            'summary' => 'Muito ágil, sortudo e atento.',
            'meta'    => [
                'movement' => [
                    'land' => 12,
                    'run_no_straight_line' => true,
                ],
                'luck' => [
                    'applies_to' => 'dexterity_skills_non_attack',
                    'pm_cost'    => 1,
                    'reroll_best_of_two' => true,
                ],
                'senses' => ['low_light_vision' => true],
                'skill_bonuses' => [
                    ['skill' => 'Percepção', 'bonus' => 2],
                ],
                'saving_throws' => [
                    ['save' => 'Reflexos', 'bonus' => 2],
                ],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'DES', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        /* ======================
            Herança do Crocodilo
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'crocodilo',
            'name'    => 'Herança do Crocodilo',
            'summary' => 'Caçador anfíbio, mordida poderosa e explosões de movimento.',
            'meta'    => [
                'natural_weapons' => [
                    ['type' => 'bite', 'damage' => '1d6', 'crit' => 'x2', 'kind' => 'piercing', 'extra_attack_pm' => 1],
                ],
                'grapple' => ['bonus' => 2],
                'movement' => ['swim' => 6],
                'defense_bonus' => 1,
                'skill_bonuses' => [
                    ['skill' => 'Furtividade', 'bonus' => 2],
                ],
                'special_actions' => [
                    'reptilian_surge' => [
                        'pm_cost' => 1,
                        'frequency' => 'once_per_scene',
                        'grants' => 'extra_move_action',
                    ],
                ],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'CON', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        /* ======================
            Herança do Gato
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'gato',
            'name'    => 'Herança do Gato',
            'summary' => 'Reflexos felinos, garras e muita sorte.',
            'meta'    => [
                'many_lives' => [
                    'adds_attribute_to' => [
                        ['test' => 'Constituição_para_estabilizar', 'attribute' => 'CAR'],
                        ['skill' => 'Acrobacia', 'attribute' => 'CAR'],
                    ],
                    'fall_damage_reduction_dice' => '3d6',
                    'requires_conscious' => true,
                ],
                'natural_weapons' => [
                    ['type' => 'claw', 'count' => 2, 'damage' => '1d6', 'crit' => 'x2', 'kind' => 'slashing', 'extra_attack_pm' => 1, 'can_use_as_offhand_for_dual_wield_styles' => true],
                ],
                'senses' => ['low_light_vision' => true],
                'skill_bonuses' => [
                    ['skill' => 'Furtividade', 'bonus' => 2],
                    ['skill' => 'Percepção',  'bonus' => 2],
                ],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'CAR', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        /* ======================
            Herança do Leão
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'leao',
            'name'    => 'Herança do Leão',
            'summary' => 'Predador régio: mordida, rugido e sentidos aguçados.',
            'meta'    => [
                'natural_weapons' => [
                    ['type' => 'bite', 'damage' => '1d8', 'crit' => 'x2', 'kind' => 'piercing', 'extra_attack_pm' => 1],
                ],
                'special_actions' => [
                    'imperious_roar' => [
                        'action' => 'move',
                        'pm_cost' => 1,
                        'area' => 'short',
                        'effect' => 'enemies_minus2_damage_rolls_1_round',
                        'tag' => 'fear',
                    ],
                ],
                'senses' => ['low_light_vision' => true],
                'skill_bonuses' => [
                    ['skill' => 'Intimidação', 'bonus' => 2],
                    ['skill' => 'Percepção',   'bonus' => 2],
                ],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'FOR', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        /* ======================
            Herança do Lobo
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'lobo',
            'name'    => 'Herança do Lobo',
            'summary' => 'Caçador de matilha: faro, mordida e táticas de flanqueamento.',
            'meta'    => [
                'senses' => ['scent' => true],
                'natural_weapons' => [
                    ['type' => 'bite', 'damage' => '1d6', 'crit' => 'x2', 'kind' => 'piercing', 'extra_attack_pm' => 1],
                ],
                'pack_tactics' => [
                    'damage_bonus_vs_flanked' => 2,
                    'threat_range_bonus_vs_flanked' => 2,
                ],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'CAR', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        /* ======================
            Herança do Morcego
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'morcego',
            'name'    => 'Herança do Morcego',
            'summary' => 'Asas, noite e eco — mobilidade e reconhecimento.',
            'meta'    => [
                'movement' => [
                    'hover' => 9,
                    'fly'   => ['speed' => 12, 'pm_per_round' => 1, 'requires_no_heavy_armor' => true],
                    'needs_space_for_wings' => true,
                    'occupy_one_size_larger_space_when_flying' => true,
                ],
                'senses' => ['darkvision' => true],
                'skill_bonuses' => [
                    ['skill' => 'Furtividade', 'bonus' => 2],
                    ['skill' => 'Percepção',   'bonus' => 2],
                ],
                'special_actions' => [
                    'echolocation' => [
                        'pm_cost' => 1,
                        'grants'  => 'blindsight',
                        'range'   => 'medium',
                        'duration_rounds' => 1,
                    ],
                ],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'DES', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        /* ======================
            Herança do Urso
        ====================== */
        $insertVariant($moreauId, [
            'key'     => 'urso',
            'name'    => 'Herança do Urso',
            'summary' => 'Força bruta e presença imponente.',
            'meta'    => [
                'size_override' => 'Grande',
                'senses' => ['scent' => true],
                'natural_weapons' => [
                    ['type' => 'bite', 'damage' => '1d6', 'crit' => 'x2', 'kind' => 'piercing', 'extra_attack_pm' => 1],
                ],
                'intimidation_uses' => ['attribute_override' => 'CON'],
            ],
        ], [
            ['mode' => 'fixed',  'attribute' => 'CON', 'modifier' => +1],
            ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2],
        ]);

        $nagahId = DB::table('races')->insertGetId([
            'slug'          => 'nagah',
            'name'          => 'Nagah',
            'size'          => 'Médio',
            'speed'         => null,
            'creature_type' => 'monstro',
            'source'        => 'T20',
            'summary'       => 'Serpentes humanoides com dádivas de Sszzaas; bônus de atributos variam entre machos e fêmeas.',
            'meta'          => json_encode([
                'natural_weapons' => [
                    ['type' => 'tail', 'damage' => '1d6', 'crit' => 'x2', 'kind' => 'bludgeoning', 'extra_attack_pm' => 1],
                ],
                'skill_bonuses' => [
                    ['skill' => 'Enganação', 'bonus' => 2],
                ],
                'special_actions' => [
                    'deception_substitute' => [
                        'pm_cost'   => 2,
                        'frequency' => 'once_per_scene',
                        'effect'    => 'substitute_any_skill_test_with_bluff',
                    ],
                ],
                'senses' => ['low_light_vision' => true],
                'defense_bonus' => 1,
                'resistances' => [
                    ['type' => 'poison', 'bonus' => 5],
                ],
                'vulnerabilities' => [
                    ['type' => 'cold', 'extra_damage_per_die' => 1],
                ],
                'penalties' => [
                    ['type' => 'bardic_music', 'save_penalty' => 5],
                ],
            ]),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Variante — Nagah (Macho)
        $insertVariant($nagahId, [
            'key'     => 'macho',
            'name'    => 'Nagah (Macho)',
            'summary' => 'Vigor serpentino: físico ágil e resistente.',
            'meta'    => ['sex' => 'male'],
        ], [
            ['mode' => 'fixed', 'attribute' => 'FOR', 'modifier' => +1],
            ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +1],
            ['mode' => 'fixed', 'attribute' => 'CON', 'modifier' => +1],
        ]);

        // Variante — Nagah (Fêmea)
        $insertVariant($nagahId, [
            'key'     => 'femea',
            'name'    => 'Nagah (Fêmea)',
            'summary' => 'Astúcia e encanto inspirados por Sszzaas.',
            'meta'    => ['sex' => 'female'],
        ], [
            ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => +1],
            ['mode' => 'fixed', 'attribute' => 'SAB', 'modifier' => +1],
            ['mode' => 'fixed', 'attribute' => 'CAR', 'modifier' => +1],
        ]);

        /* ======================
            Nezumi
        ====================== */
        $insertRace(
            [
                'slug'   => 'nezumi',
                'name'   => 'Nezumi',
                'size'   => 'Pequeno',
                'speed'  => 9, // “Pequeno, mas não metade”: mantém 9m
                // se quiser marcar como “monstro”, troque creature_type p/ 'monstro'
                // 'creature_type' => 'humanoide',
                'summary' => 'Roedores ágeis e resilientes; pequenos, mas com passo rápido e mordida perigosa.',
                'meta'   => [
                    // Empunhadura Poderosa
                    'powerful_grip' => [
                        'wield_larger_weapon_penalty' => -2,
                        'second_copy_upgrades_penalty_to' => 0,
                    ],
                    // Pequeno mas com deslocamento cheio e bônus situacionais
                    'size_special' => ['small_but_full_speed' => true],
                    'resistances'  => [
                        ['type' => 'fear', 'bonus' => 5, 'conditional' => 'vs_larger_creatures'],
                    ],
                    'skill_bonuses' => [
                        ['skill' => 'Intimidação', 'bonus' => 2],
                    ],
                    // Roedor (mordida + efeito no crítico)
                    'natural_weapons' => [
                        [
                            'type' => 'bite',
                            'damage' => '1d6',
                            'crit' => 'x2',
                            'kind' => 'slashing',
                            'extra_attack_pm' => 1,
                            'critical_effects' => [
                                'if_target_armored'   => 'damage_armor',        // deixa armadura avariada
                                'if_target_unarmored' => ['crit_multiplier_bonus' => 1], // +1 no multiplicador
                            ],
                        ],
                    ],
                    'senses' => [
                        'scent'            => true,
                        'low_light_vision' => true,
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'CON', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => -1],
            ]
        );

        /* ======================
            Ogro
        ====================== */
        $insertRace(
            [
                'slug'          => 'ogro',
                'name'          => 'Ogro',
                'size'          => 'Grande',       // tamanho Grande por padrão
                'speed'         => 9,           // usa o padrão do sistema (ex.: 9m)
                'creature_type' => 'humanoide',    // subtipo vai no meta
                'summary'       => 'Humanoide gigante, bruto e resistente; grande porte, golpeia com força descomunal.',
                'meta'          => [
                    'subtypes' => ['gigante'],
                    'senses'   => ['low_light_vision' => true],
                    'combat'   => [
                        // “… Maior a Porrada!” — 1 PM para +1d8 no ataque corpo a corpo, se acertar
                        'melee_spend_pm_bonus' => [
                            'cost_pm'         => 1,
                            'extra_damage'    => '1d8',
                            'damage_type'     => 'same_as_attack',
                            'frequency'       => 'per_attack', // seu app pode interpretar como “quando declarar no ataque”
                        ],
                    ],
                    // “Camada de Ingenuidade” — penalidades
                    'skill_penalties' => [
                        ['skill' => 'Intuição', 'penalty' => 5],
                    ],
                    'save_penalties'  => [
                        ['save'  => 'Vontade',  'penalty' => 5],
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'FOR', 'modifier' => +3],
                ['mode' => 'fixed', 'attribute' => 'CON', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => -1],
                ['mode' => 'fixed', 'attribute' => 'CAR', 'modifier' => -1],
            ]
        );

        /* ======================
            Orc
        ====================== */
        $insertRace(
            [
                'slug'          => 'orc',
                'name'          => 'Orc',
                'size'          => 'Médio',
                'speed'         => null,           // usa o padrão do sistema (ex.: 9m)
                'creature_type' => 'humanoide',
                'summary'       => 'Guerreiros implacáveis das cavernas; ferozes, resistentes e sensíveis à luz.',
                'meta'          => [
                    // Feroz — +2 dano (corpo a corpo e arremesso); se sofreu dano, vira +4 até o fim do próximo turno
                    'damage_bonuses' => [[
                        'applies_to'                     => ['melee', 'thrown'],
                        'base_bonus'                     => 2,
                        'on_taken_damage_next_turn_bonus' => 4,
                    ]],

                    // Habitante das Cavernas — visão no escuro; +2 Percepção/Sobrevivência no subterrâneo; sensibilidade à luz
                    'senses' => ['darkvision' => true],
                    'skill_bonuses' => [
                        ['skill' => 'Percepção',     'bonus' => 2, 'context' => 'subterrâneo'],
                        ['skill' => 'Sobrevivência',  'bonus' => 2, 'context' => 'subterrâneo'],
                    ],
                    'light_sensitivity' => true, // seu app trata a penalidade em ambientes de luz forte

                    // Vigor Brutal — +2 Fortitude; soma FOR ao total de PV
                    'save_bonuses' => [
                        ['save' => 'Fortitude', 'bonus' => 2],
                    ],
                    'hp_bonus_from_str' => true, // regra: adicionar o valor de FOR ao total de PV
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'FOR', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'CON', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => -1],
            ]
        );

        /* ======================
            Pteros
        ====================== */
        $insertRace(
            [
                'slug'          => 'pteros',
                'name'          => 'Pteros',
                'size'          => 'Médio',
                'speed'         => null, // usa o padrão do sistema (ex.: 9m)
                'creature_type' => 'monstro',
                'summary'       => 'Predadores alados com laço mental, garras nos pés e domínio dos céus.',
                'meta'          => [
                    // Ligação Natural — telepatia com uma criatura inteligente; alcance longo; sempre sabem direção e distância; pode trocar a cada aventura
                    'bond' => [
                        'type'        => 'natural_link',
                        'target'      => 'creature_int_-3_or_higher',
                        'range'       => 'longo',
                        'telepathy'   => true,
                        'awareness'   => ['direction' => true, 'distance' => true],
                        'switch_rule' => 'once_per_adventure',
                    ],

                    // Mãos Rudimentares — só empunha itens mágicos ou adaptados (1 dia, 50% do preço). Itens de origem/habilidades já vêm adaptados.
                    'hands_limitation' => [
                        'rudimentary'          => true,
                        'can_wield'            => ['magic', 'adapted'],
                        'adapt_time'           => '1 day',
                        'adapt_cost'           => '50% item price',
                        'starting_items_adapted' => true,
                    ],

                    // Pés Rapinantes — duas garras 1d6 (corte); 1 PM para 1 ataque extra/rodada ao agredir com outra arma
                    'natural_weapons' => [
                        [
                            'name'              => 'garras (pés)',
                            'count'             => 2,
                            'damage'            => '1d6',
                            'crit'              => 'x2',
                            'type'              => 'corte',
                            'extra_attack_pm'   => 1,
                            'requires_free_limb' => true,
                        ],
                    ],

                    // Senhor dos Céus — pairar 9m (ignora terreno difícil e imune a dano de queda enquanto consciente);
                    // 1 PM/rodada para voar 12m se não estiver com armadura pesada; em voo/pairar ocupa +1 categoria de tamanho
                    'movement' => [
                        'hover'                                   => 9,
                        'ignore_difficult_terrain_while_hovering' => true,
                        'fall_immunity_when_conscious'            => true,
                        'fly_on_pm' => [
                            'speed'               => 12,
                            'pm_per_round'        => 1,
                            'requires_no_heavy_armor' => true,
                        ],
                        'occupy_space_plus_one_size_when_wings_open' => true,
                    ],

                    // Sentidos Rapinantes
                    'senses' => [
                        'low_light_vision' => true, // visão na penumbra
                    ],
                    'skill_bonuses' => [
                        ['skill' => 'Percepção',    'bonus' => 2],
                        ['skill' => 'Sobrevivência', 'bonus' => 2],
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'SAB', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => -1],
            ]
        );

        /* ======================
            Tabrachi
        ====================== */
        $insertRace(
            [
                'slug'          => 'tabrachi',
                'name'          => 'Tabrachi',
                'size'          => 'Médio',
                'speed'         => null, // usa o padrão do sistema (ex.: 9m)
                'creature_type' => 'monstro',
                'summary'       => 'Batráquios anfíbios; língua preênsil, salto absurdo e nado veloz.',
                'meta'          => [
                    // Batráquio — visão na penumbra e natação = desloc. terrestre
                    'senses'   => ['low_light_vision' => true],
                    'movement' => [
                        'swim_equals_land' => true, // “desloc. de natação igual ao terrestre”
                    ],

                    // Linguarudo — arma natural (língua) 1d4 impacto, alcance 3m, versátil (+2 desarmar/derrubar), 1 PM p/ ataque extra/rodada ao agredir com outra arma
                    'natural_weapons' => [
                        [
                            'name'               => 'língua',
                            'damage'             => '1d4',
                            'crit'               => 'x2',
                            'type'               => 'impacto',
                            'reach_meters'       => 3,
                            'versatile_manoeuvres' => [
                                ['maneuver' => 'desarmar', 'bonus' => 2],
                                ['maneuver' => 'derrubar', 'bonus' => 2],
                            ],
                            'extra_attack_pm'    => 1,
                            'requires_agredir_with_other_weapon' => true,
                        ],
                    ],

                    // Saltador — +10 em Atletismo (saltos)
                    'athletics' => [
                        'jump_bonus' => 10,
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'CON', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'FOR', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'CAR', 'modifier' => -1],
            ]
        );

        /* ======================
            Tengu
        ====================== */
        $insertRace(
            [
                'slug'          => 'tengu',
                'name'          => 'Tengu',
                'size'          => 'Médio',
                'speed'         => null,
                'creature_type' => 'espírito',
                'summary'       => 'Corvídeos espirituais; asas desorientadoras, domínio do céu e sentidos aguçados.',
                'meta'          => [
                    // Asas Desorientadoras — benefícios de Finta Aprimorada; se já tiver, +5 na Enganação para fintar
                    'feint' => [
                        'counts_as_improved_feint' => true,
                        'if_already_has_feat_bonus_on_bluff_feint' => 5, // Enganação (fintar) +5
                    ],

                    // Caminhante do Céu — pairar 9m, ignora terreno difícil, imune a dano de queda (consciente),
                    // 1 PM/rodada para voar 12m sem armadura pesada, ocupa +1 tamanho ao abrir asas
                    'movement' => [
                        'hover'                                      => 9,
                        'ignore_difficult_terrain_while_hovering'    => true,
                        'fall_immunity_when_conscious'               => true,
                        'fly_on_pm' => [
                            'speed'                => 12,
                            'pm_per_round'         => 1,
                            'requires_no_heavy_armor' => true,
                        ],
                        'occupy_space_plus_one_size_when_wings_open' => true,
                    ],

                    // Sentidos Corvinos — espírito, visão no escuro, +2 Percepção
                    'senses'        => ['darkvision' => true],
                    'skill_bonuses' => [
                        ['skill' => 'Percepção', 'bonus' => 2],
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => +1],
            ]
        );

        /* ======================
            Velocis
        ====================== */
        $insertRace(
            [
                'slug'          => 'velocis',
                'name'          => 'Velocis',
                'size'          => 'Médio',
                'speed'         => 12, // Velocista da Planície
                'creature_type' => 'monstro',
                'summary'       => 'Felinos ágeis de planície: atravessam espinheiros, sentidos aguçados e corrida implacável.',
                'meta'          => [
                    // Através de Espinheiros
                    'resistances' => [
                        ['type' => 'corte',      'value' => 2],
                        ['type' => 'perfuração', 'value' => 2],
                    ],
                    'terrain' => [
                        'ignore_difficult_terrain_natural' => true,
                    ],

                    // Sentidos Selvagens
                    'senses' => [
                        'low_light_vision' => true,
                        'scent'            => true,
                    ],
                    'skill_bonuses' => [
                        ['skill' => 'Sobrevivência', 'bonus' => 2],
                    ],

                    // Velocista da Planície
                    'athletics' => [
                        'use_dex_as_key' => true,                 // Destreza como atributo-chave de Atletismo
                        'advantage_on'   => ['run', 'jump'],      // rola 2d e usa o melhor em correr/saltar
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'SAB', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => -1],
            ]
        );

        /* ======================
            Voracis (apenas mulheres)
        ====================== */
        $insertRace(
            [
                'slug'          => 'voracis',
                'name'          => 'Voracis',
                'size'          => 'Médio',
                'speed'         => null, // padrão do sistema (ex.: 9m)
                'creature_type' => 'monstro',
                'summary'       => 'Predadoras imponentes: garras, escalada e instinto de caça; somente mulheres.',
                'meta'          => [
                    'sex_restriction' => 'female',

                    // Garras (duas) — 1d6 corte, 1 PM p/ ataque extra/rodada; pode servir como “secundária” em estilos de duas armas
                    'natural_weapons' => [
                        [
                            'name'                 => 'garras',
                            'damage'               => '1d6',
                            'crit'                 => 'x2',
                            'type'                 => 'corte',
                            'both_hands'           => true,
                            'extra_attack_pm'      => 1,
                            'secondary_style_ok'   => true,
                        ],
                    ],

                    // Rainha da Selva
                    'movement' => ['climb' => 9],
                    'skill_bonuses' => [
                        ['skill' => 'Atletismo',     'bonus' => 2],
                        ['skill' => 'Sobrevivência', 'bonus' => 2], // vem de “Sentidos Selvagens”
                    ],
                    'rest' => [
                        'pv_per_level_bonus' => 1,  // recupera +1 PV por nível ao descansar
                    ],

                    // Sentidos Selvagens
                    'senses' => [
                        'low_light_vision' => true,
                        'scent'            => true,
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'CON', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'INT', 'modifier' => -1],
            ]
        );

        /* ======================
            Yidishan
        ====================== */
        $insertRace(
            [
                'slug'          => 'yidishan',
                'name'          => 'Yidishan',
                'size'          => 'Médio',
                'speed'         => null,
                'creature_type' => 'construto',
                'summary'       => 'Híbridos mecânicos: imunidades, repouso inerte e peças metálicas protetoras.',
                'meta'          => [
                    // Híbrido Mecânico
                    'senses'     => ['darkvision' => true],
                    'immunities' => ['cansaço', 'metabólicos', 'veneno'],
                    'vitals'     => [
                        'no_breathe'       => true,
                        'no_eat'           => true,
                        'no_sleep'         => true,
                    ],
                    'healing' => [
                        'mundane_multiplier' => 0.5, // cura mundana tem efeito pela metade
                        'no_food_items'      => true,
                    ],
                    'rest' => [
                        'requires_inert_hours' => 8,
                        'ignore_rest_conditions' => true, // não afeta por boas/ruins condições de descanso
                    ],

                    // Natureza Orgânica — guardado como instrução de escolha
                    'origin_choice' => [
                        'either' => [
                            'train_one_skill_or_general_power' => true,
                            'or_other_humanoid_race_trait'     => true, // pode herdar 1 habilidade de outra raça humanoide
                            'inherits_non_medium_size'         => true, // se a raça for de tamanho ≠ Médio, adota esse tamanho
                        ],
                    ],

                    // Peças Metálicas
                    'defense' => ['bonus' => 2],
                    'armor'   => ['penalty' => -2],
                ],
            ],
            [
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 3, 'exclusions' => ['CAR']],
                ['mode' => 'fixed',  'attribute' => 'CAR', 'modifier' => -2],
            ]
        );

        /* ======================
            Eiradaan
        ====================== */
        $insertRace(
            [
                'slug'          => 'eiradaan',
                'name'          => 'Eiradaan',
                'size'          => 'Médio',
                'speed'         => null,                 // usa o padrão do sistema (ex.: 9m)
                'creature_type' => 'espírito',
                'summary'       => 'Feéricos instintivos: magia guiada pela sabedoria, sentidos místicos e aura melancólica.',
                'meta'          => [
                    // Essência Feérica
                    'senses'        => ['low_light_vision' => true],
                    'communication' => ['speak_with_animals' => true],

                    // Magia Instintiva
                    'magic' => [
                        'arcane_key_override' => 'SAB', // pode usar SAB como atributo-chave para magias arcanas
                        'skills_key_overrides' => [
                            ['skill' => 'Misticismo', 'key' => 'SAB'],
                        ],
                        // ao lançar uma magia, ganha +1 PM para aprimoramentos (não cumulativo)
                        'augment_pm_bonus'        => 1,
                        'augment_pm_bonus_stacks' => false,
                    ],

                    // Sentidos Místicos (efeito constante)
                    'constant_effects' => ['Visão Mística (básico)'],

                    // Canção da Melancolia — desvantagem em Vontade vs. efeitos mentais
                    'saving_throws' => [
                        'will_vs_mental' => ['advantage' => false, 'disadvantage' => true],
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'SAB', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'CAR', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'FOR', 'modifier' => -1],
            ]
        );

        /* ======================
            Galokk
        ====================== */
        $insertRace(
            [
                'slug'          => 'galokk',
                'name'          => 'Galokk',
                'size'          => 'Grande',            // Meio-Gigante
                'speed'         => null,
                'creature_type' => 'humanoide (gigante)',
                'summary'       => 'Meio-gigantes forjados em provações: força titânica, estatura imponente e adaptação precoce.',
                'meta'          => [
                    // Força dos Titãs — regra parametrizada para referência mecânica
                    'titan_strength' => [
                        'trigger'        => 'melee_or_thrown_hit',
                        'pm_cost'        => 1,
                        'explode_on_max' => true,             // rola 1 dado extra ao tirar o valor máximo em um dado de dano
                        'extra_dice_cap' => ['by_attribute' => 'FOR'], // limite de dados extras = FOR
                    ],

                    // Meio-Gigante
                    'skills_key_overrides' => [
                        ['skill' => 'Intimidação', 'key' => 'FOR'], // pode usar FOR em Intimidação
                    ],

                    // Infância entre os Pequenos
                    'training' => [
                        'extra_trained_skills' => 1,
                    ],
                ],
            ],
            [
                ['mode' => 'fixed',  'attribute' => 'FOR', 'modifier' => +1],
                ['mode' => 'fixed',  'attribute' => 'CON', 'modifier' => +1],
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 1], // livre escolha (sem exclusões)
                ['mode' => 'fixed',  'attribute' => 'CAR', 'modifier' => -1],
            ]
        );

        /* ======================
            Meio-Elfo
        ====================== */
        $insertRace(
            [
                'slug'          => 'meio-elfo',
                'name'          => 'Meio-Elfo',
                'size'          => 'Médio',
                'speed'         => null, // usa padrão do sistema (ex.: 9m)
                'creature_type' => 'humanoide',
                'summary'       => 'Entre dois povos: ambição versátil, sociabilidade natural e herança élfica.',
                'meta'          => [
                    // Ambição Herdada — escolhe 1 poder (geral OU único de origem)
                    'free_power_choice' => [
                        'quantity' => 1,
                        'types'    => ['geral', 'origem_unico'],
                    ],

                    // Entre Dois Mundos — +1 em TODAS as perícias cujo atributo-chave seja CAR
                    'skill_key_global_bonus' => [
                        ['ability' => 'CAR', 'bonus' => 1],
                    ],

                    // Sangue Élfico
                    'senses'      => ['low_light_vision' => true],
                    'counts_as'   => ['Elfo'],        // considerado “Elfo” para efeitos de raça
                    'magic'       => [
                        // +1 PM em níveis ímpares (inclui 1º)
                        'pm_per_odd_level' => 1,
                    ],
                ],
            ],
            [
                ['mode' => 'fixed',  'attribute' => 'INT', 'modifier' => +1],
                ['mode' => 'choice', 'modifier' => +1, 'quantity' => 2, 'exclusions' => ['CON']],
            ]
        );

        /* ======================
            Sátiro
        ====================== */
        $insertRace(
            [
                'slug'          => 'satiro',
                'name'          => 'Sátiro',
                'size'          => 'Médio',
                'speed'         => 12,               // Pernas caprinas
                'creature_type' => 'espírito',
                'summary'       => 'Festeiros feéricos: carisma cativante, música mágica, marrada e passos ligeiros.',
                'meta'          => [
                    // Festeiro Feérico
                    'senses'         => ['low_light_vision' => true],
                    'skill_bonuses'  => [
                        ['skill' => 'Atuação',  'bonus' => 2],
                    ],
                    'saving_throws'  => [
                        'fortitude' => ['bonus' => 2],
                    ],

                    // Instrumentista Mágico — magias concedidas quando empunha instrumento
                    'magic' => [
                        'granted_spells' => [
                            ['name' => 'Amedrontar', 'circle' => 1, 'key' => 'CAR'],
                            ['name' => 'Enfeitiçar',  'circle' => 1, 'key' => 'CAR'],
                            ['name' => 'Hipnotismo',  'circle' => 1, 'key' => 'CAR'],
                            ['name' => 'Sono',        'circle' => 1, 'key' => 'CAR'],
                        ],
                        'relearn_cost_reduction_pm' => 1, // se aprender de novo, -1 PM
                        'requires_instrument'       => true,
                    ],

                    // Marrada — arma natural + ataque extra com PM
                    'natural_weapons' => [
                        [
                            'name'            => 'Marrada',
                            'type'            => 'impacto',
                            'damage'          => '1d6',
                            'crit'            => 'x2',
                            'extra_attack_pm' => 1,     // 1x/rodada, ao agredir com outra arma
                        ],
                    ],

                    // Pernas Caprinas — usa DES como atributo-chave de Atletismo
                    'skills_key_overrides' => [
                        ['skill' => 'Atletismo', 'key' => 'DES'],
                    ],
                ],
            ],
            [
                ['mode' => 'fixed', 'attribute' => 'CAR', 'modifier' => +2],
                ['mode' => 'fixed', 'attribute' => 'DES', 'modifier' => +1],
                ['mode' => 'fixed', 'attribute' => 'SAB', 'modifier' => -1],
            ]
        );
    }
}
