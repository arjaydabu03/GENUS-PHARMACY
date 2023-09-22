<?php

namespace App\Models;

use App\Filters\DepartmentFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "department";

    protected $fillable = ["sync_id", "code", "name", "company_id"];

    public function company()
    {
        return $this->hasOne(Company::class, "sync_id", "company_id");
    }

    protected string $default_filters = DepartmentFilter::class;
}
