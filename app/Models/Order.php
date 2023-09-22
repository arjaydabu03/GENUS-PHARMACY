<?php

namespace App\Models;

use App\Filters\OrderFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $table = "order";

    protected $fillable = [
        "transaction_id",
        "requestor_id",

        "order_no",
        "customer_code",

        "material_id",
        "material_code",
        "material_name",

        "category_id",
        "category_name",

        "uom_id",
        "uom_code",

        "quantity",
        "remarks",
    ];
    protected string $default_filters = OrderFilter::class;

    protected $hidden = ["created_at", "updated_at"];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function time()
    {
        return $this->belongsTo(Order::class);
    }
}
