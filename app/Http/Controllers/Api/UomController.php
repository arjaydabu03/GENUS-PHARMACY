<?php

namespace App\Http\Controllers\Api;

use App\Models\Uom;
use App\Response\Status;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\Uom\CodeRequest;
use App\Http\Requests\Uom\StoreRequest;

class UomController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;

        $uom = Uom::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        $is_empty = $uom->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(Status::UOM_DISPLAY, $uom);
    }

    public function show($id)
    {
        $uom = Uom::where("id", $id)->get();

        if ($uom->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        return GlobalFunction::response_function(
            Status::UOM_DISPLAY,
            $uom->first()
        );
    }

    public function store(StoreRequest $request)
    {
        $uom = Uom::create([
            "code" => $request["code"],
            "description" => $request["description"],
        ]);
        return GlobalFunction::save(Status::UOM_SAVE, $uom);
    }

    public function update(StoreRequest $request, $id)
    {
        $uom = Uom::find($id);

        $not_found = Uom::where("id", $id)->get();

        if ($not_found->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        $uom->update([
            "code" => $request["code"],
            "description" => $request["description"],
        ]);

        return GlobalFunction::response_function(Status::UOM_UPDATE, $uom);
    }

    public function destroy($id)
    {
        $uom = Uom::where("id", $id)
            ->withTrashed()
            ->get();

        if ($uom->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $uom = Uom::withTrashed()->find($id);
        $is_active = Uom::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $uom->delete();
            $message = Status::ARCHIVE_STATUS;
        } else {
            $uom->restore();
            $message = Status::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $uom);
    }
    public function code_validate(CodeRequest $request)
    {
        return GlobalFunction::response_function(Status::SINGLE_VALIDATION);
    }
}
