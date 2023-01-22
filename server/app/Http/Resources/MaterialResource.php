<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'price' => $this->price,
            'category' => $this->category,
            'type' => $this->type,
            'unit_id' => $this->unit_id,
            'tax_id' => $this->tax_id,
            'currency_id' => $this->currency_id,
            'unit' => $this->unit,
            'tax' => $this->tax,
            'currency' => $this->currency,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
