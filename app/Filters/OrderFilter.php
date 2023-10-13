<?php

namespace App\Filters;

use Carbon\Carbon;
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
        $date_today = Carbon::now()
            ->timeZone("Asia/Manila")
            ->format("Y-m-d");

        $this->builder
            ->when($status === "posted", function ($query) use ($date_today) {
                $query->whereHas("transaction", function ($query) use (
                    $date_today
                ) {
                    return $query
                        ->whereNotNull("date_posted")
                        ->whereDate("date_ordered", $date_today);
                });
            })
            ->when($status === "all", function ($query) {
                $query->whereHas("transaction", function ($query) {
                    $query->whereNotNull("date_posted");
                });
            });
    }

    public function from($from)
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
