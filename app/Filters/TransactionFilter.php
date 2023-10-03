<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class TransactionFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        "order_no",

        "date_needed",
        "date_ordered",
        "date_posted",
        "reason",

        "company_id",
        "company_code",
        "company_name",

        "department_id",
        "department_code",
        "department_name",

        "location_id",
        "location_code",
        "location_name",

        "customer_id",
        "customer_code",
        "customer_name",

        "charge_company_id",
        "charge_company_code",
        "charge_company_name",

        "charge_department_id",
        "charge_department_code",
        "charge_department_name",

        "charge_location_id",
        "charge_location_code",
        "charge_location_name",

        "requestor_id",
        "requestor_name",
        "rush",
        "type",
        "batch_no",
    ];
    public function status($status)
    {
        $this->builder
            ->when($status === "pending", function ($query) {
                $query->whereNull("date_posted");
            })
            ->when($status === "posted", function ($query) {
                $query->whereNotNull("date_posted");
            })
            ->when($status === "archive", function ($query) {
                $query->onlyTrashed();
            })
            ->when($status === "all", function ($query) {
                $query->withTrashed();
            });
    }

    public function report_filter($report)
    {
        $this->builder->when($report === "posted", function ($query) {
            $query->whereNotNull("date_posted");
        });
        // ->when($report === "all", function ($query) {
        //     $query->whereNotNull("date_posted");
        // });
    }

    public function from($from)
    {
        $this->builder->whereDate("date_ordered", ">=", $from);
    }
    public function to($to)
    {
        $this->builder->whereDate("date_ordered", "<=", $to);
    }
}
