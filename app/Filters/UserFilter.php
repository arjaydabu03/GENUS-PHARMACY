<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UserFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        "account_code",
        "account_name",
        "company_code",
        "company",
        "department_code",
        "department",
        "location_code",
        "location",
        "mobile_no",
        "username",
        "role_id",
    ];
}
