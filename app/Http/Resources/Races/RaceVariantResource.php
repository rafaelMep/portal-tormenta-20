<?php

namespace App\Http\Resources\Races;

use App\Http\Resources\Races\RaceVariantAttributeModResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RaceVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'key'     => $this->key,
            'name'    => $this->name,
            'summary' => $this->summary,
            'meta'    => $this->meta,
            'attribute_mods' => RaceVariantAttributeModResource::collection(
                $this->whenLoaded('attributeMods')
            ),
        ];
    }
}
