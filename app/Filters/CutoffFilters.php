<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class CutoffFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["name", "time"];
}
