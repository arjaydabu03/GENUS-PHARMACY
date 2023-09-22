<?php

namespace App\Models;

use App\Filters\CompanyFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "company";

    protected $fillable = ["sync_id", "code", "name"];

    protected string $default_filters = CompanyFilter::class;
}
