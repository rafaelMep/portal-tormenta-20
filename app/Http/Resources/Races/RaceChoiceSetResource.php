<?php

namespace App\Http\Resources\Races;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceChoiceSetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'key'        => $this->key,
            'label'      => $this->label,
            'min_picks'  => $this->min_picks,
            'max_picks'  => $this->max_picks,
            'constraints' => $this->constraints,
            'options'    => RaceChoiceSetOptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
