<?php

namespace App\Http\Resources\Races;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceVariantAttributeModResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'race_variant_id'  => $this->race_variant_id,
            'choice_option_id' => $this->choice_option_id,
            'attribute'        => $this->attribute,
            'modifier'         => $this->modifier,
            'quantity'         => $this->quantity,
            'exclusions'       => $this->exclusions,
            'notes'            => $this->notes,
        ];
    }
}
