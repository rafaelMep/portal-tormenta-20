<?php

namespace App\Domain\Characters;

class AbilityScoreCalculator
{
    public function applyRacial(array $base, array $racialMods): array
    {
        $final = $base;

        foreach ($racialMods as $m) {
            $attr = $m['attribute'];
            if (!isset($final[$attr])) continue;
            $final[$attr] += $m['modifier'];
        }

        return $final;
    }
}
