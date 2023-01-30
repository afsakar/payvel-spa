<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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

        // TODO: Refactor currency and account_type

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'balance' => $this->balance,
            'account_type_id' => $this->account_type_id,
            'currency_id' => $this->currency_id,
            'currency' => $this->currency ?? null,
            'account_type' => $this->accountType ? $this->accountType->name : null,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
