<?php

namespace App\Http\Resources\Races;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceChoiceGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'key'          => $this->key,
            'name'         => $this->name,
            'min_choices'  => $this->min_choices,
            'max_choices'  => $this->max_choices,
            'required'     => $this->required,
            'sort'         => $this->sort,
            'meta'         => $this->meta,
            'options'      => RaceChoiceGroupOptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
