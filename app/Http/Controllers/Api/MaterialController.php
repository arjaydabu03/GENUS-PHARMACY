<?php

namespace App\Http\Controllers\Api;

use App\Models\Uom;
use App\Models\Category;
use App\Models\Material;
use App\Response\Status;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;
use App\Http\Requests\Material\CodeRequest;
use App\Http\Requests\Material\StoreRequest;
use App\Http\Requests\Material\ImportRequest;

class MaterialController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;

        $material = Material::with("category", "uom")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        $is_empty = $material->isEmpty();
        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        return GlobalFunction::response_function(
            Status::MATERIAL_DISPLAY,
            $material
        );
    }

    public function show($id)
    {
        $material = Material::with("category")
            ->where("id", $id)
            ->get();
        if ($material->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }
        return GlobalFunction::response_function(
            Status::MATERIAL_DISPLAY,
            $material->first()
        );
    }

    public function store(StoreRequest $request)
    {
        // return $request;
        $material = Material::create([
            "code" => $request["code"],
            "name" => $request["name"],
            "category_id" => $request["category_id"],
            "uom_id" => $request["uom_id"],
        ]);
        $material = $material
            ->with("category", "uom")
            ->firstWhere("id", $material->id);
        return GlobalFunction::save(Status::MATERIAL_SAVE, $material);
    }

    public function update(StoreRequest $request, $id)
    {
        $not_found = Material::where("id", $id)->get();

        if ($not_found->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $material = Material::find($id);

        $material->update([
            "code" => $request["code"],
            "name" => $request["name"],
            "category_id" => $request["category_id"],
            "uom_id" => $request["uom_id"],
        ]);

        $material = $material
            ->with("category", "uom")
            ->firstWhere("id", $material->id);
        return GlobalFunction::response_function(
            Status::MATERIAL_UPDATE,
            $material
        );
    }

    public function destroy($id)
    {
        $material = Material::where("id", $id)
            ->withTrashed()
            ->get();

        if ($material->isEmpty()) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        $material = Material::withTrashed()->find($id);
        $is_active = Material::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $material->delete();
            $message = Status::ARCHIVE_STATUS;
        } else {
            $material->restore();
            $message = Status::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $material);
    }

    public function validate_code(CodeRequest $request)
    {
        return GlobalFunction::response_function(Status::SINGLE_VALIDATION);
    }

    public function import_material(ImportRequest $request)
    {
        $import = $request->all();

        foreach ($import as $file_import) {
            $code = $file_import["code"];
            $name = $file_import["name"];
            $uom = $file_import["uom"];
            $category = $file_import["category"];

            $category_id = Category::where("name", $category)->first();

            $uom_id = Uom::where("code", $uom)->first();

            $material = Material::create([
                "code" => $code,
                "name" => $name,
                "category_id" => $category_id->id,
                "uom_id" => $uom_id->id,
            ]);
        }
        return GlobalFunction::save(
            Status::MATERIAL_IMPORT,
            $material->orderByDesc("created_at")->get()
        );
    }
}
