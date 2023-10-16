<?php

namespace App\Http\Resources;

use App\Http\Resources\OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "id" => $this->id,
            "order_no" => $this->order_no,
            "type" => $this->type,
            "batch_no" => $this->batch_no,
            "rush" => $this->rush,

            "dates" => [
                "date_ordered" => $this->date_ordered,
                "date_needed" => $this->date_needed,
                "date_posted" => $this->date_posted,
            ],
            "reason" => $this->reason,
            "type" => [
                "id" => $this->type_id,
                "name" => $this->type_name,
            ],
            "company" => [
                "id" => $this->company_id,
                "code" => $this->company_code,
                "name" => $this->company_name,
            ],
            "department" => [
                "id" => $this->department_id,
                "code" => $this->department_code,
                "name" => $this->department_name,
            ],
            "location" => [
                "id" => $this->location_id,
                "code" => $this->location_code,
                "name" => $this->location_name,
            ],
            "customer" => [
                "id" => $this->customer_id,
                "code" => $this->customer_code,
                "name" => $this->customer_name,
            ],
            "charge_company" => [
                "id" => $this->charge_company_id,
                "code" => $this->charge_company_code,
                "name" => $this->charge_company_name,
            ],
            "charge_department" => [
                "id" => $this->charge_department_id,
                "code" => $this->charge_department_code,
                "name" => $this->charge_department_name,
            ],
            "charge_location" => [
                "id" => $this->charge_location_id,
                "code" => $this->charge_location_code,
                "name" => $this->charge_location_name,
            ],
            "requestor" => [
                "id" => $this->requestor_id,
                "name" => $this->requestor_name,
            ],

            "order" => OrderResource::collection($this->orders),
        ];
    }
}
