<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends BillController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return Inertia::render('Home', [
            "bills" => parent::index()
        ]);
    }

    public function store(Request $request)
    {
        parent::store($request);
        return to_route('home.index');
    }
}
