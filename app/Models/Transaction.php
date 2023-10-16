<?php

namespace App\Models;

use App\Filters\TransactionFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $hidden = ["created_at"];

    protected $fillable = [
        "order_no",

        "date_needed",
        "date_ordered",
        "date_posted",
        "reason",

        "company_id",
        "company_code",
        "company_name",

        "department_id",
        "department_code",
        "department_name",

        "location_id",
        "location_code",
        "location_name",

        "customer_id",
        "customer_code",
        "customer_name",

        "charge_company_id",
        "charge_company_code",
        "charge_company_name",

        "charge_department_id",
        "charge_department_code",
        "charge_department_name",

        "charge_location_id",
        "charge_location_code",
        "charge_location_name",

        "requestor_id",
        "requestor_name",
        "rush",
        "type_id",
        "type_name",
        "batch_no",
    ];

    public function orders()
    {
        return $this->hasMany(
            Order::class,
            "transaction_id",
            "id"
        )->withTrashed();
    }

    // public function elixir_orders()
    // {
    //     return $this->belongsToMany(
    //         Order::class,
    //         "transaction_id",
    //         "id"
    //     )->withTrashed();
    // }

    protected string $default_filters = TransactionFilter::class;
}
