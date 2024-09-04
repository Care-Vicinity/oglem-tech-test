<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillController extends Controller {

    public function index() {

        $data = User::query()
            ->leftJoin("bill_user as bu", "bu.user_id", "=", "users.id")
            ->leftJoin("bills as b", "b.id", "=", "bu.bill_id")
            ->leftJoin("bill_stages as bs", "bs.id", "=", "b.bill_stage_id")
            ->groupBy("users.id")
            ->select(["users.id", "users.name"])
            ->selectRaw("count(distinct b.id) as 'totalBills'")
            ->selectRaw("count(distinct if(bs.label='Submitted',b.id,null)) as 'totalSubmittedBills'")
            ->selectRaw("count(distinct if(bs.label='On Hold',b.id,null)) as 'totalOnHoldBills'")
            ->selectRaw("count(distinct if(bs.label='Approved',b.id,null)) as 'totalApprovedBills'")
            ->orderBy("users.name", 'asc')
            ->get();

        return $data;
    }

    public function store(Request $request) {
        $bill = new Bill();
        $bill->fill($request->all());
        $bill['submitted_at'] = Carbon::now();
        $bill->save();

        return $bill;
    }
}
