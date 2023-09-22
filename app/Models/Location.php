<?php

namespace App\Models;

use App\Filters\LocationFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "location";

    protected $fillable = ["sync_id", "code", "name", "deleted_at"];

    protected string $default_filters = LocationFilter::class;

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            "location_department",
            "location_id",
            "department_id",
            "sync_id",
            "sync_id"
        );
    }
}
