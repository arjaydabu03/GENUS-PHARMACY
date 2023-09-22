<?php

namespace App\Http\Controllers\API;

use App\Models\Cutoff;

use App\Response\Status;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\Cutoff\StoreRequest;

class CutoffController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;

        $cut_off = Cutoff::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        $is_empty = $cut_off->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Status::CUT_OFF_DISPLAY,
            $cut_off
        );
    }

    public function show($id)
    {
        return Cutoff::where("id", $id)->get();
    }
    public function store(StoreRequest $request)
    {
        $cut_off = Cutoff::create([
            "name" => $request["name"],
            "time" => $request["time"],
        ]);
        return GlobalFunction::save(Status::CUTOFF_SAVE, $cut_off);
    }

    public function update(StoreRequest $request, $id)
    {
        $cut_off = Cutoff::find($id);

        $not_found = Cutoff::where("id", $id)->get();

        if ($not_found->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $cut_off->update([
            "name" => $request["name"],
            "time" => $request["time"],
        ]);
        return GlobalFunction::response_function(Status::CUTOFF_SAVE, $cut_off);
    }
    public function destroy($id)
    {
        $cut_off = Cutoff::where("id", $id)
            ->withTrashed()
            ->get();

        if ($cut_off->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $cut_off = Cutoff::withTrashed()->find($id);
        $is_active = Cutoff::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $cut_off->delete();
            $message = Status::ARCHIVE_STATUS;
        } else {
            $cut_off->restore();
            $message = Status::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $cut_off);
    }
}
