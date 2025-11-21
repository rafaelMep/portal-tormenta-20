<?php

namespace App\Http\Resources\Races;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceChoiceSetOptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'value' => $this->value,
            'label' => $this->label,
            'meta'  => $this->meta,
        ];
    }
}
