<?php

namespace Tests\Unit;

use App\Models\Bill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BillUnitTest extends TestCase
{
    use DatabaseTransactions;

    public function test_save_bill(): void
    {
        $startCount = Bill::all()->count();
        $bill = new Bill();
        $bill->fill([
            "id" => null,
            'bill_reference' => "test1",
            'bill_date' => Carbon::now(),
            'bill_stage_id' => 2
        ]);
        $bill->save();
        $endCount = Bill::all()->count();
        $this->assertEquals($startCount + 1, $endCount, "Added Record");
        $this->assertNotNull($bill->id, "bill given id");
    }

    public function test_save_bill_users()
    {

        $bills = Bill::all();
        $this->assertGreaterThan(0, $bills->count(), "bills should exist");
        $users = User::all();
        $this->assertGreaterThan(0, $users->count(), "users should exist");

        $bill = $bills[0];
        $user = $users[0];

        $startCount = $bill->users()->count();
        $bill->users()->attach($user);
        $endCount = $bill->users()->count();

        $this->assertGreaterThan($startCount, $endCount, "User should be added");
    }

    public function test_bill_stage()
    {
        $bills = Bill::all();
        $this->assertGreaterThan(0, $bills->count(), "bills should exist");

        $bill = $bills[0];
        $stage = $bill->billStage();

        $this->assertNotNull($stage, "Stage should exist");
    }
}
