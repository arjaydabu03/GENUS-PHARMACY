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
}
