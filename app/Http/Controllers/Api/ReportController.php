<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return Transaction::with("order")->get();
    }
}
