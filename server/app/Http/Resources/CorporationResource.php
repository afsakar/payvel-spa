<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CorporationResource extends JsonResource
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
            'currency_id' => $this->currency_id,
            'name' => $this->name,
            'owner' => $this->owner,
            'tel_number' => $this->tel_number,
            'gsm_number' => $this->gsm_number,
            'fax_number' => $this->fax_number,
            'email' => $this->email,
            'address' => $this->address,
            'tax_office' => $this->tax_office,
            'tax_number' => $this->tax_number,
            'type' => $this->type,
            'currency' => $this->currency ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
