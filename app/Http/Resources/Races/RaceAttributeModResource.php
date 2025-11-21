<?php

namespace App\Http\Resources\Races;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceAttributeModResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'choice_option_id' => $this->choice_option_id,
            'mode'            => $this->mode,
            'attribute'       => $this->attribute,
            'modifier'        => $this->modifier,
            'quantity'        => $this->quantity,
            'exclusions'      => $this->exclusions,
            'notes'           => $this->notes,
        ];
    }
}
