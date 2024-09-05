<?php

namespace Tests\Feature;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class BillFeatureTest extends TestCase
{

    use WithoutMiddleware;
    use DatabaseTransactions;

    public function test_inertia_response(): void
    {
        $response = $this->get("/");

        $this->assertStringContainsString("data-page", $response->getContent(), "Data response present");
        $response->assertStatus(200);
        $response->assertInertia();
    }

    public function test_post_bill(): void
    {
        $response = $this->postJson("/", [
            "id" => null,
            "bill_reference" => "test",
            "bill_date" => "2024-09-04T22:01:41",
            "bill_stage_id" => 2
        ]);
        $this->assertStringContainsString("Redirecting to", $response->getContent(), "Redirect present");
        $response->assertStatus(302);
    }

    public function test_assigning_bills_to_users()
    {
        $this->artisan("app:assign-bills-to-users")->assertOk()->expectsOutputToContain("No Bills to Assign");

        for ($i = 0; $i < 100; $i++) {
            $bill = new Bill();
            $bill->fill([
                "id" => null,
                'bill_reference' => "test1",
                'bill_date' => Carbon::now(),
                'bill_stage_id' => 2
            ]);
            $bill->save();
        }

        $this->artisan("app:assign-bills-to-users")->assertFailed()->expectsOutputToContain("Not enough users to");
    }
}
