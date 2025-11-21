<?php

namespace App\Http\Resources\Races;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Races\RaceVariantResource;
use App\Http\Resources\Races\RaceAttributeModResource;
use App\Http\Resources\Races\RaceChoiceSetResource;
use App\Http\Resources\Races\RaceChoiceGroupResource;

class RaceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'slug'          => $this->slug,
            'name'          => $this->name,
            'size'          => $this->size,
            'speed'         => $this->speed,
            'creature_type' => $this->creature_type,
            'summary'       => $this->summary,
            'meta'          => $this->meta,

            'variants'       => RaceVariantResource::collection($this->whenLoaded('variants')),
            'attribute_mods' => RaceAttributeModResource::collection($this->whenLoaded('attributeMods')),
            'choice_sets'    => RaceChoiceSetResource::collection($this->whenLoaded('choiceSets')),
            'choice_groups'  => RaceChoiceGroupResource::collection($this->whenLoaded('choiceGroups')),
        ];
    }
}
