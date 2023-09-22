<?php

namespace App\Models;

use App\Filters\CutoffFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cutoff extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "cut_off";

    protected string $default_filters = CutoffFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = ["name", "time"];
}
