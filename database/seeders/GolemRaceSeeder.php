<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GolemRaceSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $slug = 'golem';
            $raceId = DB::table('races')->where('slug', $slug)->value('id');

            if (!$raceId) {
                $raceId = DB::table('races')->insertGetId([
                    'slug'          => $slug,
                    'name'          => 'Golem',
                    'size'          => 'Médio',
                    'speed'         => null, // definido pelo chassi (vários = 6m)
                    'creature_type' => 'construto',
                    'source'        => 'T20',
                    'summary'       => 'Construtos animados com diferentes chassis, fontes de energia e maravilhas mecânicas.',
                    'meta'          => json_encode(['notes' => 'Escolhe chassi, fonte de energia, tamanho e 1 maravilha.']),
                    'is_official'   => true,
                    'created_by_id' => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // Base do Golem: FOR +1, CAR -1
            $baseMods = [
                ['attribute' => 'FOR', 'modifier' => +1],
                ['attribute' => 'CAR', 'modifier' => -1],
            ];

            foreach ($baseMods as $m) {
                // evita duplicar se já seedou antes
                $exists = DB::table('race_attribute_mods')
                    ->where('race_id', $raceId)
                    ->whereNull('choice_option_id')
                    ->where('attribute', $m['attribute'])
                    ->where('modifier', $m['modifier'])
                    ->exists();
                if (!$exists) {
                    DB::table('race_attribute_mods')->insert([
                        'race_id'       => $raceId,
                        'choice_option_id' => null,
                        'mode'          => 'fixed',
                        'attribute'     => $m['attribute'],
                        'modifier'      => $m['modifier'],
                        'quantity'      => 1,
                        'exclusions'    => null,
                        'notes'         => 'Base da raça Golem',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // Helper para criar grupo
            $mkGroup = function (string $key, string $name, array $meta = [], int $min = 1, int $max = 1, bool $required = true, int $sort = 0) use ($raceId) {
                $gid = DB::table('race_choice_groups')
                    ->where('race_id', $raceId)
                    ->where('key', $key)
                    ->value('id');

                if (!$gid) {
                    $gid = DB::table('race_choice_groups')->insertGetId([
                        'race_id'     => $raceId,
                        'key'         => $key,
                        'name'        => $name,
                        'min_choices' => $min,
                        'max_choices' => $max,
                        'required'    => $required,
                        'sort'        => $sort,
                        'meta'        => $meta ? json_encode($meta) : null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
                return $gid;
            };

            // Helper para criar opção (com mods e efeitos textuais)
            $mkOption = function (int $groupId, string $key, string $name, ?string $summary, array $meta = [], array $attrMods = [], array $effects = [], int $sort = 0) use ($raceId) {
                $oid = DB::table('race_choice_options')
                    ->where('group_id', $groupId)
                    ->where('key', $key)
                    ->value('id');

                if (!$oid) {
                    $oid = DB::table('race_choice_options')->insertGetId([
                        'group_id'      => $groupId,
                        'key'           => $key,
                        'name'          => $name,
                        'summary'       => $summary,
                        'meta'          => $meta ? json_encode($meta) : null,
                        'sort'          => $sort,
                        'is_official'   => true,
                        'created_by_id' => null,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }

                // Mods de atributo dessa opção
                foreach ($attrMods as $m) {
                    DB::table('race_attribute_mods')->insert([
                        'race_id'         => $raceId,
                        'choice_option_id' => $oid,
                        'mode'            => $m['mode'] ?? 'fixed',   // fixed | choice
                        'attribute'       => $m['attribute'] ?? null, // null quando 'choice'
                        'modifier'        => $m['modifier'],
                        'quantity'        => $m['quantity'] ?? 1,
                        'exclusions'      => isset($m['exclusions']) ? json_encode($m['exclusions']) : null,
                        'notes'           => $m['notes'] ?? null,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }

                // Efeitos textuais (explicativos) dessa opção
                foreach ($effects as $i => $textOrArr) {
                    $text = is_array($textOrArr) ? ($textOrArr['text'] ?? '') : (string) $textOrArr;
                    $tags = is_array($textOrArr) ? ($textOrArr['tags'] ?? null) : null;

                    DB::table('race_effect_texts')->insert([
                        'race_id'          => $raceId,
                        'choice_option_id' => $oid,
                        'text'             => $text,
                        'tags'             => $tags ? json_encode($tags) : null,
                        'sort'             => $i,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }

                return $oid;
            };

            /*
             |---------------------------------------------------------
             | Grupos do Golem
             |---------------------------------------------------------
             */
            $gChassis = $mkGroup('chassis', 'Chassi', meta: [
                'help' => 'Você acopla uma “carcaça/material” com efeitos específicos.',
            ], min: 1, max: 1, required: true, sort: 10);

            $gEnergy  = $mkGroup('energy',  'Fonte de Energia', meta: [
                'help' => 'Escolha como você é animado: alquímica, elemental, sagrada, vapor...',
            ], min: 1, max: 1, required: true, sort: 20);

            $gSize    = $mkGroup('size',    'Tamanho', meta: [
                'help' => 'Pequeno (+1 DES), Médio (—), Grande (-1 DES).',
            ], min: 1, max: 1, required: true, sort: 30);

            $gMarvel  = $mkGroup('marvel',  'Maravilha Mecânica', meta: [
                'help' => 'Escolha 1 Maravilha (pode ganhar outras por patamar no futuro).',
                'pick_on_creation' => 1
            ], min: 0, max: 1, required: false, sort: 40);

            /*
             |---------------------------------------------------------
             | Opções: CHASSI
             | (Só numericamos onde faz sentido agora; o resto fica em texto/meta)
             |---------------------------------------------------------
             */
            // Barro: CON +2; speed “não reduz por terreno difícil”; “passa por espaço apertado”; dependência de água
            $mkOption(
                $gChassis,
                'barro',
                'Barro',
                'CON +2; deslocamento não afetado por terreno difícil; contorção em espaços apertados; precisa de água diária.',
                meta: ['speed_override' => 6, 'notes' => ['no_terrain_difficult_penalty' => true, 'squeeze_ok' => true, 'water_dependency' => true]],
                attrMods: [['attribute' => 'CON', 'modifier' => +2]],
                effects: [
                    'Seu deslocamento não é afetado por terreno difícil e você passa automaticamente por espaços apertados.',
                    'Se ficar >1 dia sem contato com água, não recupera PM com descanso.',
                ],
                sort: 10
            );

            // Bronze: +1 em dois atributos (escolha); sem redução de armadura pesada; armadura não é acoplada
            $mkOption(
                $gChassis,
                'bronze',
                'Bronze',
                '+1 em dois atributos à sua escolha; armaduras pesadas não reduzem deslocamento; armadura NÃO é acoplada.',
                meta: ['notes' => ['heavy_armor_no_speed_penalty' => true, 'armor_is_not_acoplada' => true]],
                attrMods: [['mode' => 'choice', 'modifier' => +1, 'quantity' => 2]],
                effects: [
                    'Sua armadura não é acoplada (pode vestir/remover normalmente).',
                    'Armaduras pesadas não reduzem deslocamento.',
                ],
                sort: 20
            );

            // Carne: CON +2, FOR +1, CAR –1; speed 6 e sem redução por armadura/carga; imunidade a metamorfose e trevas; penalidade contra fogo/frio mágico
            $mkOption(
                $gChassis,
                'carne',
                'Carne',
                'CON +2, FOR +1, CAR –1; deslocamento 6m (não reduz por armadura/carga); imunidades específicas; fogo/frio mágicos deixam lento.',
                meta: ['speed_override' => 6, 'notes' => ['no_armor_speed_penalty' => true, 'no_carry_speed_penalty' => true, 'immune' => ['metamorfose', 'trevas'], 'slow_by' => ['fogo_magico', 'frio_magico']]],
                attrMods: [
                    ['attribute' => 'CON', 'modifier' => +2],
                    ['attribute' => 'FOR', 'modifier' => +1],
                    ['attribute' => 'CAR', 'modifier' => -1],
                ],
                effects: [
                    'Imunidade a metamorfose e trevas.',
                    'Dano mágico de fogo e frio deixa você lento por 1d4 rodadas.',
                ],
                sort: 30
            );

            // Ferro: FOR +1, CON +1; speed 6 e sem redução por armadura/carga; +2 Defesa; penalidade de armadura –2
            $mkOption(
                $gChassis,
                'ferro',
                'Ferro',
                'FOR +1, CON +1; deslocamento 6m (não reduz por armadura/carga); +2 Defesa; penalidade de armadura –2.',
                meta: ['speed_override' => 6, 'notes' => ['no_armor_speed_penalty' => true, 'no_carry_speed_penalty' => true, 'defense_bonus' => +2, 'armor_penalty' => -2]],
                attrMods: [
                    ['attribute' => 'FOR', 'modifier' => +1],
                    ['attribute' => 'CON', 'modifier' => +1],
                ],
                effects: [
                    'Você recebe +2 na Defesa, mas possui penalidade de armadura –2.',
                ],
                sort: 40
            );

            // Gelo Eterno: CON +2; speed 6 (não reduz por armadura/carga); imunidade a frio; redução fogo 10
            $mkOption(
                $gChassis,
                'gelo-eterno',
                'Gelo Eterno',
                'CON +2; deslocamento 6m (não reduz por armadura/carga); imunidade a frio; redução de fogo 10.',
                meta: ['speed_override' => 6, 'notes' => ['immune' => ['frio'], 'resistance' => ['fogo' => 10]]],
                attrMods: [['attribute' => 'CON', 'modifier' => +2]],
                effects: [
                    'Imunidade a frio; redução de fogo 10.',
                ],
                sort: 50
            );

            // Pedra: CON +2; speed 6; não pode correr; redução corte/fogo/perfuração 5
            $mkOption(
                $gChassis,
                'pedra',
                'Pedra',
                'CON +2; deslocamento 6m; não pode correr; redução 5 contra corte, fogo e perfuração.',
                meta: ['speed_override' => 6, 'notes' => ['no_run' => true, 'reduction' => ['corte' => 5, 'fogo' => 5, 'perfuração' => 5]]],
                attrMods: [['attribute' => 'CON', 'modifier' => +2]],
                effects: [
                    'Você não pode correr; recebe redução 5 contra corte/fogo/perfuração.',
                ],
                sort: 60
            );

            // Sucata: FOR +1, CON +1; speed 6; recuperação extra com cuidados de Ofício (artesão)
            $mkOption(
                $gChassis,
                'sucata',
                'Sucata',
                'FOR +1, CON +1; deslocamento 6m; recuperação de PV +2/nível com cuidados de Ofício (artesão).',
                meta: ['speed_override' => 6, 'notes' => ['craft_care_bonus_hp_per_level' => +2]],
                attrMods: [
                    ['attribute' => 'FOR', 'modifier' => +1],
                    ['attribute' => 'CON', 'modifier' => +1],
                ],
                effects: [
                    'Com “Cuidados Prolongados” (Ofício: artesão), sua recuperação de PV aumenta em +2 por nível naquele dia.',
                ],
                sort: 70
            );

            // Mashin: +1 em dois atributos à escolha; 2 perícias treinadas (uma pode virar maravilha)
            $mkOption(
                $gChassis,
                'mashin',
                'Mashin',
                '+1 em dois atributos; 2 perícias treinadas (uma pode virar Maravilha). Tamanho sempre Médio.',
                meta: ['locked_size' => 'Médio', 'followup' => ['attr_choice' => 2, 'skills' => 2, 'skill_to_marvel' => true]],
                attrMods: [['mode' => 'choice', 'modifier' => +1, 'quantity' => 2]],
                effects: [
                    'Você se torna treinado em duas perícias (uma pode ser trocada por uma Maravilha Mecânica).',
                    'Sempre de tamanho Médio.',
                ],
                sort: 80
            );

            // Dourado: CAR +2, FOR +1; marcar culpado (1 PM) e +1d6 luz 1/rodada contra alvo marcado
            $mkOption(
                $gChassis,
                'dourado',
                'Dourado',
                'CAR +2, FOR +1; pode marcar culpado (1 PM) e causar +1d6 de luz 1/rodada contra ele.',
                meta: ['notes' => ['guilt_mark' => true, 'extra_light_damage' => '1d6/round']],
                attrMods: [
                    ['attribute' => 'CAR', 'modifier' => +2],
                    ['attribute' => 'FOR', 'modifier' => +1],
                ],
                effects: [
                    'Gasta 1 PM para marcar um “culpado”; 1x/rodada, um ataque seu contra ele causa +1d6 de luz.',
                ],
                sort: 90
            );

            // Espelhos: CAR +2, SAB +1, CON –1; copiar habilidade de classe (1 PM)
            $mkOption(
                $gChassis,
                'espelhos',
                'Espelhos',
                'CAR +2, SAB +1, CON –1; copiar habilidade de classe visível (1 PM) até o próximo turno.',
                meta: ['notes' => ['mirror_copy' => true]],
                attrMods: [
                    ['attribute' => 'CAR', 'modifier' => +2],
                    ['attribute' => 'SAB', 'modifier' => +1],
                    ['attribute' => 'CON', 'modifier' => -1],
                ],
                effects: [
                    'Quando vê alguém usar habilidade de classe em alcance curto, pode gastar 1 PM para copiá-la até o próximo turno.',
                ],
                sort: 100
            );

            /*
             |---------------------------------------------------------
             | Opções: FONTE DE ENERGIA
             |---------------------------------------------------------
             */
            $mkOption(
                $gEnergy,
                'alquimica',
                'Alquímica',
                'Mistura alquímica lhe dá vida. Ação padrão para ingerir item alquímico: recupera 1 PM.',
                meta: ['energy' => 'alquimica'],
                effects: [
                    'Ação padrão: ingerir item alquímico para recuperar 1 PM.',
                ],
                sort: 10
            );

            $mkOption(
                $gEnergy,
                'elemental-agua',
                'Elemental (Água)',
                'Espírito elemental (frio). Imune a frio; dano de frio mágico cura metade.',
                meta: ['energy' => 'elemental', 'element' => 'frio'],
                effects: [
                    'Imune a frio; dano mágico de frio cura metade.',
                ],
                sort: 20
            );

            $mkOption(
                $gEnergy,
                'elemental-ar',
                'Elemental (Ar)',
                'Espírito elemental (eletricidade). Imune a eletricidade; dano elétrico mágico cura metade.',
                meta: ['energy' => 'elemental', 'element' => 'eletricidade'],
                effects: ['Imune a eletricidade; dano mágico de eletricidade cura metade.'],
                sort: 30
            );

            $mkOption(
                $gEnergy,
                'elemental-fogo',
                'Elemental (Fogo)',
                'Espírito elemental (fogo). Imune a fogo; dano de fogo mágico cura metade.',
                meta: ['energy' => 'elemental', 'element' => 'fogo'],
                effects: ['Imune a fogo; dano mágico de fogo cura metade.'],
                sort: 40
            );

            $mkOption(
                $gEnergy,
                'elemental-terra',
                'Elemental (Terra)',
                'Espírito elemental (ácido). Imune a ácido; dano de ácido mágico cura metade.',
                meta: ['energy' => 'elemental', 'element' => 'ácido'],
                effects: ['Imune a ácido; dano mágico de ácido cura metade.'],
                sort: 50
            );

            $mkOption(
                $gEnergy,
                'sagrada',
                'Sagrada',
                'Animado por texto/símbolo sagrado. Pode lançar 1 magia divina de 1º círculo (atributo SAB).',
                meta: ['energy' => 'sagrada', 'grants_spell' => ['circle' => 1, 'school' => 'divina', 'key_attr' => 'SAB']],
                effects: [
                    'Pode lançar 1 magia divina de 1º círculo (atributo-chave Sabedoria).',
                    'Ritual (Religião) de 1 dia pode trocar a magia (gastando pergaminho).',
                ],
                sort: 60
            );

            $mkOption(
                $gEnergy,
                'vapor',
                'Vapor',
                'Imune a fogo; dano de fogo mágico aumenta seu deslocamento por 1 rodada; pode soprar jato de vapor (cone 4,5m, 1d6/PM).',
                meta: ['energy' => 'vapor', 'steam_breath' => true],
                effects: [
                    'Imune a fogo; fogo mágico aumenta seu deslocamento por 1 rodada.',
                    'Ação padrão: jato de vapor (cone 4,5m) causando 1d6 por PM gasto (Ref CD CON 1/2).',
                ],
                sort: 70
            );

            /*
             |---------------------------------------------------------
             | Opções: TAMANHO
             |---------------------------------------------------------
             */
            $mkOption(
                $gSize,
                'pequeno',
                'Pequeno',
                'DES +1. (Override de tamanho para Pequeno.)',
                meta: ['size_override' => 'Pequeno'],
                attrMods: [['attribute' => 'DES', 'modifier' => +1]],
                sort: 10
            );

            $mkOption(
                $gSize,
                'medio',
                'Médio',
                'Sem ajustes. (Override de tamanho para Médio.)',
                meta: ['size_override' => 'Médio'],
                attrMods: [],
                sort: 20
            );

            $mkOption(
                $gSize,
                'grande',
                'Grande',
                'DES –1. (Override de tamanho para Grande.)',
                meta: ['size_override' => 'Grande'],
                attrMods: [['attribute' => 'DES', 'modifier' => -1]],
                sort: 30
            );

            /*
             |---------------------------------------------------------
             | Opções: MARAVILHA MECÂNICA (algumas de exemplo)
             |---------------------------------------------------------
             */
            $mkOption(
                $gMarvel,
                'arma-acoplada',
                'Arma Acoplada',
                'Uma arma integrada ao corpo (não pode ser desarmada). Pode ser substituída por artesão (1h, T$100).',
                effects: [
                    'Arma fica recolhida e não pode ser desarmada; pode ser trocada (1h, T$ 100) por artesão.',
                ],
                sort: 10
            );

            $mkOption(
                $gMarvel,
                'arma-elemental',
                'Arma Elemental',
                'Ação de movimento e 2 PM: arma causa +1d6 do tipo de sua fonte elemental até o fim da cena.',
                effects: [
                    '+1d6 do tipo da sua Fonte de Energia (até fim da cena, ação de movimento, 2 PM).',
                ],
                sort: 20
            );

            $mkOption(
                $gMarvel,
                'pernas-aprimoradas',
                'Pernas Aprimoradas',
                'Gaste 2 PM para +6m de deslocamento e +5 em Atletismo até o fim da cena.',
                effects: ['2 PM: +6m deslocamento e +5 Atletismo até o fim da cena.'],
                sort: 30
            );

            $mkOption(
                $gMarvel,
                'canalizar-reparos',
                'Canalizar Reparos',
                'Ação completa: gastar PM para recuperar 5 PV por PM.',
                effects: ['Ação completa: 5 PV por PM gasto.'],
                sort: 40
            );

            $mkOption(
                $gMarvel,
                'auxilio-de-mira',
                'Auxílio de Mira',
                'Em ataque à distância, pagar 1 PM: +2 na margem de ameaça desse ataque.',
                effects: ['1 PM: +2 à margem de ameaça em um ataque à distância.'],
                sort: 50
            );

            $mkOption(
                $gMarvel,
                'canhao-energetico',
                'Canhão Energético',
                'Se sua arma acoplada for arma de fogo: ação de mov. + 1 PM: próximo ataque causa +1 dado de dano (cumulativo, limit. CON).',
                effects: [
                    'Se arma acoplada for de fogo: ação de mov. + 1 PM => próximo ataque +1 dado de dano (cumulativo, até CON).'
                ],
                sort: 60
            );

            /*
             |---------------------------------------------------------
             | Efeito base comum ao Golem (texto)
             |---------------------------------------------------------
             */
            DB::table('race_effect_texts')->insert([
                'race_id'     => $raceId,
                'choice_option_id' => null,
                'text'        => 'Criatura Artificial: você é do tipo construto; visão no escuro; imunidade a cansaço, metabólicos e veneno; não respira, se alimenta ou dorme. Cura mundana não funciona; precisa ficar inerte 8h para recuperar PV/PM. Perícia Cura não funciona em você; use Ofício (artesão).',
                'tags'        => json_encode(['tipo:construto', 'imunidades', 'recarga']),
                'sort'        => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
