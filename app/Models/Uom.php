<?php

namespace App\Models;

use App\Filters\UomFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Uom extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "uom";

    protected $fillable = ["code", "description"];

    protected $hidden = ["created_at"];

    protected string $default_filters = UomFilter::class;
}
