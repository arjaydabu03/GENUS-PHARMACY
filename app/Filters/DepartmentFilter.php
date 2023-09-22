<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class DepartmentFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["code", "name"];

    // protected array $relationSearch = [
    //     "company" => ["sync_id"],
    // ];

    public function company_id($company_id)
    {
        $this->builder->when($company_id, function ($query) use ($company_id) {
            return $query->whereHas("company", function ($query) use (
                $company_id
            ) {
                return $query->where("sync_id", $company_id);
            });
        });
    }
}
