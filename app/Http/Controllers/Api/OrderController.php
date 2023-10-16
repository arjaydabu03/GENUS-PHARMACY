<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Response\Status;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ElixirResource;
use App\Http\Requests\Order\ReasonRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\Transaction\StoreRequest;
use App\Http\Requests\Transaction\UpdateRequest;
use App\Http\Requests\Transaction\DisplayRequest;
use App\Http\Resources\ElixirTransactionResource;

class OrderController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $order = Transaction::with("orders")
            ->where("requestor_id", Auth::id())
            ->useFilters()
            ->dynamicPaginate();

        $is_empty = $order->isEmpty();
        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        TransactionResource::collection($order);

        return GlobalFunction::response_function(Status::ORDER_DISPLAY, $order);
    }

    public function pharmacy_order(Request $request)
    {
        $transaction = Order::with("transaction")->get();

        if ($transaction->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $order_list = ElixirResource::collection($transaction);
        return GlobalFunction::api_response($order_list);
    }

    public function show($id)
    {
        $order = Transaction::with("orders")
            ->where("id", $id)
            ->get();
        if ($order->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $order_collection = TransactionResource::collection($order);

        return GlobalFunction::response_function(
            Status::ORDER_DISPLAY,
            $order_collection->first()
        );
    }

    public function store(StoreRequest $request)
    {
        $user = Auth()->user();

        $transaction = Transaction::create([
            "order_no" => $request["order_no"],
            "date_needed" => date("Y-m-d", strtotime($request["date_needed"])),
            "date_ordered" => Carbon::now()
                ->timeZone("Asia/Manila")
                ->format("Y-m-d"),

            "rush" => $request["rush"],
            "type_id" => $request["type"]["id"],
            "type_name" => $request["type"]["name"],
            "batch_no" => $request["batch_no"],

            "company_id" => $request["company"]["id"],
            "company_code" => $request["company"]["code"],
            "company_name" => $request["company"]["name"],

            "department_id" => $request["department"]["id"],
            "department_code" => $request["department"]["code"],
            "department_name" => $request["department"]["name"],

            "location_id" => $request["location"]["id"],
            "location_code" => $request["location"]["code"],
            "location_name" => $request["location"]["name"],

            "customer_id" => $request["customer"]["id"],
            "customer_code" => $request["customer"]["code"],
            "customer_name" => $request["customer"]["name"],

            "charge_company_id" => $request["charge_company"]["id"],
            "charge_company_code" => $request["charge_company"]["code"],
            "charge_company_name" => $request["charge_company"]["name"],

            "charge_department_id" => $request["charge_department"]["id"],
            "charge_department_code" => $request["charge_department"]["code"],
            "charge_department_name" => $request["charge_department"]["name"],

            "charge_location_id" => $request["charge_location"]["id"],
            "charge_location_code" => $request["charge_location"]["code"],
            "charge_location_name" => $request["charge_location"]["name"],

            "requestor_id" => $request["requestor"]["id"],
            "requestor_name" => $request["requestor"]["name"],
        ]);

        foreach ($request->order as $key => $value) {
            Order::create([
                "transaction_id" => $transaction->id,
                "requestor_id" => $request["requestor"]["id"],

                "order_no" => $request["order_no"],

                "customer_code" => $request["customer"]["code"],

                "material_id" => $request["order"][$key]["material"]["id"],
                "material_code" => $request["order"][$key]["material"]["code"],
                "material_name" => $request["order"][$key]["material"]["name"],

                "uom_id" => $request["order"][$key]["uom"]["id"],
                "uom_code" => $request["order"][$key]["uom"]["code"],

                "category_id" => $request["order"][$key]["category"]["id"],
                "category_name" => $request["order"][$key]["category"]["name"],

                "quantity" => $request["order"][$key]["quantity"],
                "remarks" => $request["order"][$key]["remarks"],
            ]);
        }

        return GlobalFunction::save(Status::ORDER_SAVE, $request->toArray());
    }

    public function update(UpdateRequest $request, $id)
    {
        $transaction = Transaction::find($id);
        $user = Auth()->user();
        $orders = $request->order;

        $not_found = Transaction::where("id", $id)->exists();
        if (!$not_found) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $invalid = Transaction::where("id", $id)
            ->where("requestor_id", $user->id)
            ->whereNull("date_posted")
            ->get();

        if ($invalid->isEmpty()) {
            return GlobalFunction::invalid(Status::ACCESS_DENIED);
        }

        $invalid_update = $transaction->whereNotNull("date_posted");
        if (!$invalid_update) {
            return GlobalFunction::invalid(Status::INVALID_UPDATE_POSTED);
        }

        $transaction->update([
            "charge_company_id" => $request["charge_company"]["id"],
            "charge_company_code" => $request["charge_company"]["code"],
            "charge_company_name" => $request["charge_company"]["name"],

            "charge_department_id" => $request["charge_department"]["id"],
            "charge_department_code" => $request["charge_department"]["code"],
            "charge_department_name" => $request["charge_department"]["name"],

            "charge_location_id" => $request["charge_location"]["id"],
            "charge_location_code" => $request["charge_location"]["code"],
            "charge_location_name" => $request["charge_location"]["name"],

            "rush" => $request["rush"],
            "type_id" => $request["type"]["id"],
            "type_name" => $request["type"]["name"],
            "batch_no" => $request["batch_no"],

            "date_needed" => date("Y-m-d", strtotime($request["date_needed"])),
        ]);

        $newOrders = collect($orders)
            ->pluck("id")
            ->toArray();
        $currentOrders = Order::where("transaction_id", $id)
            ->get()
            ->pluck("id")
            ->toArray();

        foreach ($currentOrders as $order_id) {
            if (!in_array($order_id, $newOrders)) {
                Order::where("id", $order_id)->forceDelete();
            }
        }

        foreach ($orders as $index => $value) {
            Order::withTrashed()->updateOrCreate(
                [
                    "id" => $value["id"] ?? null,
                ],
                [
                    "transaction_id" => $transaction["id"],
                    "requestor_id" => $transaction["requestor_id"],

                    "order_no" => $request["order_no"],
                    "customer_code" => $request["customer"]["code"],

                    "material_id" => $value["material"]["id"],
                    "material_code" => $value["material"]["code"],
                    "material_name" => $value["material"]["name"],

                    "category_id" => $value["category"]["id"],
                    "category_name" => $value["category"]["name"],

                    "uom_id" => $value["uom"]["id"],
                    "uom_code" => $value["uom"]["code"],

                    "quantity" => $value["quantity"],
                    "remarks" => $value["remarks"],
                ]
            );
        }

        $order_collection = new TransactionResource($transaction);

        return GlobalFunction::response_function(
            Status::TRANSACTION_UPDATE,
            $order_collection
        );
    }

    public function to_post(Request $request, $id)
    {
        $user = Auth()->user();
        $transaction = Transaction::where("id", $id)->get();

        if ($transaction->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $transaction = Transaction::where("id", $id);

        $not_allowed = $transaction
            ->when($user->role_id == 2, function ($query) use ($user) {
                return $query->where("requestor_id", $user->id);
            })
            ->get();
        if ($not_allowed->isEmpty()) {
            return GlobalFunction::denied(Status::ACCESS_DENIED);
        }

        $transaction->update([
            "date_posted" => date("Y-m-d H:i:s"),
        ]);

        return GlobalFunction::response_function(
            Status::TRANSACTION_POSTED,
            $transaction
        );
    }

    // Cancel transaction
    public function cancelTransaction(ReasonRequest $request, $id)
    {
        $user = Auth()->user();

        $reason = $request->reason;
        $transaction = Transaction::where("id", $id);

        $not_found = $transaction->get();
        if ($not_found->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $not_allowed = $transaction
            ->when($user->role_id != 2, function ($query) use ($user) {
                return $query->where("requestor_id", $user->id);
            })
            ->whereNull("date_posted")
            ->get();
        if ($not_allowed->isEmpty()) {
            return GlobalFunction::denied(Status::ACCESS_DENIED);
        }

        $result = $transaction
            ->get()
            ->first()
            ->update([
                "reason" => $request->reason,
            ]);

        Transaction::withTrashed()
            ->where("id", $id)
            ->delete();
        Order::where("transaction_id", $id)->delete();

        return GlobalFunction::response_function(
            Status::ARCHIVE_STATUS,
            $result
        );
    }
    //cancel order
    public function cancelOrder(Request $request, $id)
    {
        $user = Auth()->user();
        $user_scope = User::where("id", $user->id)
            ->with("scope_order")
            ->first()
            ->scope_order->pluck("location_code");

        $order = Order::with("transaction")->where("id", $id);

        $not_found = $order->get();
        if ($not_found->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $not_allowed = Order::with("transaction")
            ->where("id", $id)
            ->when($user->role_id == 2, function ($query) use ($user_scope) {
                return $query->whereIn("customer_code", $user_scope);
            })
            ->whereHas("transaction", function ($query) {
                return $query->whereNull("date_posted");
            })
            ->get();
        if ($not_allowed->isEmpty()) {
            return GlobalFunction::response_function(Status::ACCESS_DENIED);
        }

        $check_siblings = Order::where(
            "transaction_id",
            $order->get()->first()->transaction_id
        )->get();
        if ($check_siblings->count() > 1) {
            $order = $order->get()->first();

            Order::where("id", $id)->delete();

            return GlobalFunction::response_function(
                Status::ARCHIVE_STATUS,
                $order
            );
        }
        Transaction::where("id", $order->get()->first()->transaction_id)
            ->get()
            ->first()
            ->update([
                "approver_id" => $user->id,
                "approver_name" => $user->account_name,
                "date_posted" => date("Y-m-d H:i:s"),
            ]);

        Transaction::withTrashed()
            ->where("id", $order->get()->first()->transaction_id)
            ->delete();
        Order::where(
            "transaction_id",
            $order->get()->first()->transaction_id
        )->delete();

        return GlobalFunction::response_function(
            Status::ARCHIVE_STATUS,
            $order
                ->withTrashed()
                ->get()
                ->first()
        );
    }
}
