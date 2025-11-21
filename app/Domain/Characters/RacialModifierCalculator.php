<?php

namespace App\Domain\Characters;

use App\Models\Race;
use App\Models\RaceVariant;

class RacialModifierCalculator
{
    /**
     * @param Race $race
     * @param RaceVariant|null $variant
     * @param array<int,array{set_id:int,key:string,picks:array<int,string>}> $setPicks
     * @param array<int,array{group_id:int,option_ids:array<int,int>}> $groupPicks
     * @return array<int,array{attribute:string, modifier:int, source:string}>
     */
    public function calculate(
        Race $race,
        ?RaceVariant $variant,
        array $setPicks,
        array $groupPicks
    ): array {
        $mods = [];

        foreach ($race->attributeMods as $m) {
            if ($m->mode === 'fixed' && !$m->choice_option_id && $m->attribute) {
                $mods[] = [
                    'attribute' => $m->attribute,
                    'modifier'  => $m->modifier,
                    'source'    => 'Raça: ' . $race->name,
                ];
            }
        }

        if (!empty($groupPicks)) {
            $pickedIds = collect($groupPicks)
                ->flatMap(fn($g) => $g['option_ids'])
                ->filter()
                ->unique()
                ->all();

            if ($pickedIds) {
                foreach ($race->attributeMods as $m) {
                    if ($m->choice_option_id && $m->attribute && in_array($m->choice_option_id, $pickedIds, true)) {
                        $mods[] = [
                            'attribute' => $m->attribute,
                            'modifier'  => $m->modifier,
                            'source'    => 'Escolha de raça',
                        ];
                    }
                }
            }
        }

        foreach ($setPicks as $setPick) {
            foreach ($setPick['picks'] as $attr) {
                $mods[] = [
                    'attribute' => $attr,
                    'modifier'  => 1,
                    'source'    => 'Escolha: ' . $setPick['key'],
                ];
            }
        }

        if ($variant) {
            if ($variant->relationLoaded('attributeMods') && $variant->attributeMods->count()) {
                foreach ($variant->attributeMods as $m) {
                    if ($m->attribute) {
                        $mods[] = [
                            'attribute' => $m->attribute,
                            'modifier'  => $m->modifier,
                            'source'    => 'Variante: ' . $variant->name,
                        ];
                    }
                }
            } elseif (is_array($variant->meta['attribute_mods'] ?? null)) {
                foreach ($variant->meta['attribute_mods'] as $m) {
                    $mods[] = [
                        'attribute' => $m['attribute'],
                        'modifier'  => $m['modifier'],
                        'source'    => 'Variante: ' . $variant->name,
                    ];
                }
            }
        }

        return $mods;
    }
}
