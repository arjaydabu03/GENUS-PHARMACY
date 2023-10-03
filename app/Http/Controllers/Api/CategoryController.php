<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Response\Status;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\Category\StoreRequest;

class CategoryController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;

        $category = Category::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();
        $is_empty = $category->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Status::CATEGORY_DISPLAY,
            $category
        );
    }

    public function show($id)
    {
        $category = Category::where("id", $id)->get();

        if ($category->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        return GlobalFunction::response_function(
            Status::CATEGORY_DISPLAY,
            $category->first()
        );
    }

    public function store(StoreRequest $request)
    {
        $category = Category::create([
            "name" => $request["name"],
        ]);
        return GlobalFunction::save(Status::CATEGORY_SAVE, $category);
    }

    public function update(StoreRequest $request, $id)
    {
        $category = Category::find($id);

        $not_found = Category::where("id", $id)->get();

        if ($not_found->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        $category->update([
            "name" => $request["name"],
        ]);

        return GlobalFunction::response_function(
            Status::CATEGORY_UPDATE,
            $category
        );
    }

    public function destroy($id)
    {
        $category = Category::where("id", $id)
            ->withTrashed()
            ->get();

        if ($category->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $category = Category::withTrashed()->find($id);
        $is_active = Category::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $category->delete();
            $message = Status::ARCHIVE_STATUS;
        } else {
            $category->restore();
            $message = Status::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $category);
    }
}
