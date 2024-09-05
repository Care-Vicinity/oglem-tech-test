<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class BillController extends Controller
{

    public function index()
    {
        return Bill::fetchBillSummary();
    }

    public function store(Request $request)
    {

        $bill = new Bill();
        $bill->fill($request->all());
        try {
            $bill->bill_date = Carbon::parse($bill->bill_date);
        } catch (Exception $ex) {
            throw new Exception("Invalid Bill Date format");
        }
        $bill['submitted_at'] = Carbon::now();
        $bill->save();

        return $bill;
    }
}
