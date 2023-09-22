<?php

namespace App\Models;

use App\Filters\MaterialFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "material";

    protected $fillable = ["code", "name", "category_id", "uom_id"];

    protected $hidden = ["category_id", "uom_id", "created_at", "deleted_at"];

    protected string $default_filters = MaterialFilter::class;

    public function category()
    {
        return $this->belongsTo(Category::class)
            ->select("id", "name", "deleted_at")
            ->withTrashed();
    }

    public function uom()
    {
        return $this->belongsTo(UOM::class)
            ->select("id", "code", "description", "deleted_at")
            ->withTrashed();
    }
}
