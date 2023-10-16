<?php

namespace App\Models;

use App\Filters\TypeFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    use HasFactory, softDeletes, Filterable;

    protected $table = "type";

    protected $fillable = ["name"];

    protected $hidden = ["created_at"];

    protected string $default_filters = TypeFilter::class;
}
