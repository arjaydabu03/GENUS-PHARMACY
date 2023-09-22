<?php

namespace App\Http\Controllers\Api;
use App\Models\Company;
use App\Response\Status;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;

class CompanyController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;

        $company = Company::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        $is_empty = $company->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Status::COMPANY_DISPLAY,
            $company
        );
    }
    public function store(Request $request)
    {
        $sync = $request->all();

        $company = Company::upsert(
            $sync,
            ["sync_id"],
            ["code", "name", "deleted_at"]
        );

        return GlobalFunction::save(
            Status::COMPANY_IMPORT,
            $request->toArray()
        );
    }
}
