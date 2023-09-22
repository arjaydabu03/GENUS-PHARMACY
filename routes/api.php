<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UomController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\API\CutoffController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\DepartmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::post("logout", [UserController::class, "logout"]);
    Route::patch("user/{id}", [UserController::class, "destroy"]);
    Route::post("code_validate", [UserController::class, "code_validate"]);
    Route::post("validate_username", [
        UserController::class,
        "validate_username",
    ]);
    Route::post("validate_mobile", [UserController::class, "validate_mobile"]);
    Route::post("validate_name", [UserController::class, "validate_name"]);
    Route::put("user/reset/{id}", [UserController::class, "reset_password"]);

    Route::put("user/old_password/{id}", [
        UserController::class,
        "old_password",
    ]);
    Route::put("user/change_password/", [
        UserController::class,
        "change_password",
    ]);
    Route::put("user/{id}", [UserController::class, "update"]);
    Route::apiResource("user", UserController::class);
    Route::get("transaction/notification", [OrderController::class, "count"]);

    Route::patch("category/{id}", [CategoryController::class, "destroy"]);
    Route::apiResource("category", CategoryController::class);

    Route::patch("material/{id}", [MaterialController::class, "destroy"]);
    Route::apiResource("material", MaterialController::class);
    Route::post("validate_code", [MaterialController::class, "validate_code"]);
    Route::post("import", [MaterialController::class, "import_material"]);

    Route::patch("posted/{id}", [OrderController::class, "to_post"]);
    Route::apiResource("order", OrderController::class);
    Route::patch("order/cancel/{id}", [OrderController::class, "cancelOrder"]);
    Route::patch("transaction/cancel/{id}", [
        OrderController::class,
        "cancelTransaction",
    ]);
    Route::get("transaction/notification", [OrderController::class, "count"]);
    // Route::get("elixir_order", [OrderController::class, "elixir_order"]);

    Route::post("uom/validate", [UomController::class, "code_validate"]);
    Route::patch("uom/{id}", [UomController::class, "destroy"]);
    Route::apiResource("uom", UomController::class);

    Route::post("warehouse/validate", [
        WarehouseController::class,
        "code_validate",
    ]);

    Route::post("role/validate", [RoleController::class, "validate_name"]);
    Route::patch("role/{id}", [RoleController::class, "destroy"]);
    Route::apiResource("role", RoleController::class);

    // Route::get("report", [ReportController::class, "view"]);
    // Route::get("count", [ReportController::class, "count"]);
    // Route::get("request_count", [ReportController::class, "requestor_count"]);
    // Route::get("export", [ReportController::class, "export"]);
    // Route::patch("serve/{id}", [ReportController::class, "serve"]);

    Route::patch("cut_off/{id}", [CutoffController::class, "destroy"]);
    Route::apiResource("cut_off", CutoffController::class);

    Route::apiResource("company", CompanyController::class);
    Route::apiResource("department", DepartmentController::class);
    Route::apiResource("location", LocationController::class);
});

Route::post("login", [UserController::class, "login"]);
// Route::get("reports", [ReportController::class, "view"]);
// Route::get("users", [UserController::class, "index"]);
