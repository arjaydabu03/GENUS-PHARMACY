<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ElixirTransactionResource;

class ElixirResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "transaction_id" => $this->id,

            "order_details" => [
                "farm_code" => $this->customer_code,
                "farm_name" => $this->customer_name,
                "date_ordered" => $this->date_ordered,
                "date_needed" => $this->date_needed,
                "order_no" => $this->order_no,
                "type" => $this->type,
                "batch_no" => $this->batch_no,
            ],
            "order" => [
                "itemCode" => $this->material_code,
                "itemDescription" => $this->material_name,

                "code" => $this->uom_code,
                "quantity" => $this->quantity,
                "category" => $this->category_name,
            ],
        ];
    }
}
