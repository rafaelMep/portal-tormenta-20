<?php

namespace App\Http\Resources\Races;

use Illuminate\Http\Resources\Json\JsonResource;

class RaceChoiceGroupOptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'key'     => $this->key,
            'name'    => $this->name,
            'summary' => $this->summary,
            'meta'    => $this->meta,
        ];
    }
}
