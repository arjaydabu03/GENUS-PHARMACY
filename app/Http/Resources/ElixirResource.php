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
                "farm_name" => $this->transaction->customer_name,
                "orderNo" => $this->order_no,
                "dateOrdered" => date(
                    "Y-m-d",
                    strtotime($this->transaction->date_ordered)
                ),
                "dateNeeded" => date(
                    "Y-m-d",
                    strtotime($this->transaction->date_needed)
                ),

                "type" => $this->transaction->type_name,
                "batchNo" => $this->transaction->batch_no,
                "order" => [
                    "itemCode" => $this->material_code,
                    "itemDescription" => $this->material_name,
                    "uom" => $this->uom_code,
                    "quantity" => $this->quantity,
                    "category" => $this->category_name,
                ],
            ],
        ];
    }
}
