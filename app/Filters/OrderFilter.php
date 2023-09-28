<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class OrderFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        "transaction_id",
        "requestor_id",

        "order_no",
        "customer_code",

        "material_id",
        "material_code",
        "material_name",

        "category_id",
        "category_name",

        "uom_id",
        "uom_code",

        "quantity",
        "remarks",
    ];
    protected array $relationSearch = [
        "transction" => ["date_posted"],
    ];

    public function status($status)
    {
        $this->builder
            ->when($status === "pending", function ($query) {
                $query->whereHas("transaction", function ($query) {
                    $query->whereNull("date_posted");
                });
            })
            ->when($status === "posted", function ($query) {
                $query->whereHas("transaction", function ($query) {
                    $query->whereNotNull("date_posted");
                });
            })
            ->when($status === "all", function ($query) {
                $query->withTrashed();
            });
    }

    public function hello($from)
    {
        $this->builder->whereHas("transaction", function ($query) use ($from) {
            $query->whereDate("date_ordered", ">=", $from);
        });
    }
    public function to($to)
    {
        $this->builder->whereHas("transaction", function ($query) use ($to) {
            $query->whereDate("date_ordered", "<=", $to);
        });
    }
}
