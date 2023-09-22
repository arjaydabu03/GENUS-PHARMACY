<?php

namespace App\Models;

use App\Filters\CategoryFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "categories";

    protected $fillable = ["name"];

    protected $hidden = ["created_at"];

    protected string $default_filters = CategoryFilter::class;
}
