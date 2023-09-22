<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Response\Status;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;

        $department = Department::with("company")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();

        $is_empty = $department->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Status::DEPARTMENT_DISPLAY,
            $department
        );
    }
    public function store(Request $request)
    {
        $sync = $request->all();

        $department = Department::upsert(
            $sync,
            ["sync_id"],
            ["code", "name", "company_id", "deleted_at"]
        );

        return GlobalFunction::save(
            Status::DEPARTMENT_IMPORT,
            $request->toArray()
        );
    }
}
