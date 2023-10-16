<?php

namespace App\Http\Controllers\Api;

use App\Models\Type;
use App\Response\Status;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\Type\StoreRequest;

class TypeController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;

        $type = Type::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();
        $is_empty = $type->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(Status::TYPE_DISPLAY, $type);
    }

    public function show($id)
    {
        $type = Type::where("id", $id)->get();

        if ($type->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        return GlobalFunction::response_function(
            Status::TYPE_DISPLAY,
            $type->first()
        );
    }

    public function store(StoreRequest $request)
    {
        $type = Type::create([
            "name" => $request["name"],
        ]);
        return GlobalFunction::save(Status::TYPE_SAVE, $type);
    }

    public function update(StoreRequest $request, $id)
    {
        $type = Type::find($id);

        $not_found = Type::where("id", $id)->get();

        if ($not_found->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        $type->update([
            "name" => $request["name"],
        ]);

        return GlobalFunction::response_function(Status::TYPE_UPDATE, $type);
    }

    public function destroy($id)
    {
        $type = Type::where("id", $id)
            ->withTrashed()
            ->get();

        if ($type->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $type = Type::withTrashed()->find($id);
        $is_active = Type::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $type->delete();
            $message = Status::ARCHIVE_STATUS;
        } else {
            $type->restore();
            $message = Status::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $type);
    }
}
