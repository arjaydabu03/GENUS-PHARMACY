<?php

namespace App\Models;

use App\Filters\RoleFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "role";

    protected $fillable = ["name", "access_permission"];

    protected $casts = ["access_permission" => "json"];

    protected $hidden = ["created_at"];

    protected string $default_filters = RoleFilter::class;
}
