<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class MaterialFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["code", "name", "category_id", "uom_id"];
}
