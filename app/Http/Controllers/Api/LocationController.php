<?php

namespace App\Http\Controllers\Api;
use App\Models\Location;
use App\Response\Status;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisplayRequest;

class LocationController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;
        $location = Location::with("departments")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->useFilters()
            ->dynamicPaginate();
            

        $is_empty = $location->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Status::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Status::LOCATION_DISPLAY,
            $location
        );
    }
    public function store(Request $request)
    {
        $sync_all = $request->all();

        foreach ($sync_all as $location) {
            $sync_id = $location["sync_id"];
            $code = $location["code"];
            $name = $location["name"];
            $deleted_at = $location["deleted_at"];

            $locations = Location::withTrashed()->updateOrCreate(
                [
                    "sync_id" => $sync_id,
                ],
                [
                    "sync_id" => $sync_id,
                    "code" => $code,
                    "name" => $name,
                    "deleted_at" => $deleted_at,
                ]
            );

            $locations->departments()->sync($location["departments"]);
        }

        return GlobalFunction::save(
            Status::LOCATION_IMPORT,
            $request->toArray()
        );
    }
}
