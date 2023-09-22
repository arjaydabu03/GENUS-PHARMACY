<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class LocationFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["code", "name"];

    protected array $allowedSorts = ["updated_at"];

    protected array $relationSearch = [
        "departments" => ["sync_id"],
    ];

    // public function department_id($department_id)
    // {
    //     $this->builder->when($department_id, function ($query) use (
    //         $department_id
    //     ) {
    //         return $query->whereHas("departments", function ($query) use (
    //             $department_id
    //         ) {
    //             return $query->where("sync_id", $department_id);
    //         });
    //     });
    // }
}
