<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'slug'       => $this->slug,
            'name'       => $this->name,
            'key_attr'   => $this->attr_key,
            'trained_only'  => $this->trained_only,
            'armor_penalty' => $this->armor_penalty,
        ];
    }
}
