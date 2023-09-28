<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Order;
use App\Response\Status;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\DisplayRequest;

class ReportController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $transaction = Transaction::with("orders")
            ->useFilters()
            ->dynamicPaginate();

        if ($transaction->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Status::ORDER_DISPLAY,
            $transaction
        );
    }

    public function export(Request $request)
    {
        $order = Order::with("transaction")
            ->useFilters()
            ->dynamicPaginate();

        if ($order->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        return GlobalFunction::response_function(Status::DATA_EXPORT, $order);
    }

    public function requestor_count(Request $request)
    {
        $date_today = Carbon::now()
            ->timeZone("Asia/Manila")
            ->format("Y-m-d");

        $requestor_id = Auth()->id();

        $pending = Transaction::whereNull("date_posted")
            ->where("requestor_id", $requestor_id)
            ->get()
            ->count();
        $posted = Transaction::whereNotNull("date_posted")
            ->where("requestor_id", $requestor_id)
            ->get()
            ->count();
        $all = Transaction::where("requestor_id", $requestor_id)
            ->get()
            ->count();

        $count = [
            "pending" => $pending,
            "posted" => $posted,
            "all" => $all,
        ];

        return GlobalFunction::response_function(Status::COUNT_DISPLAY, $count);
    }
}
