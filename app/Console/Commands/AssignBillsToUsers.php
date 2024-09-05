<?php

namespace App\Console\Commands;

use App\Enums\BillStageEnum;
use App\Enums\BillStatusEnum;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Console\Command;

class AssignBillsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-bills-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unassignedBills = Bill::whereDoesntHave('users')->whereHas('billStage', fn($query) => $query->where('label', BillStageEnum::SUBMITTED))->get();

        if ($unassignedBills->count() <= 0) {
            $this->info("No Bills to Assign");
            return;
        }

        $users = User::query()
            ->leftJoin("bill_user as bu", "bu.user_id", "=", "users.id")
            ->leftJoin("bills as b", "b.id", "=", "bu.bill_id")
            ->leftJoin("bill_stages as bs", "bs.id", "=", "b.bill_stage_id")
            ->groupBy("users.id")
            ->select(["users.id", "users.name"])
            ->selectRaw("count(distinct if(bs.label=?,b.id,null)) as 'totalSubmittedBills'", [BillStageEnum::SUBMITTED])
            ->orderByRaw('totalSubmittedBills asc, users.name asc')
            ->havingRaw('totalSubmittedBills < 3')
            ->get();

        if ($users->count() <= 0) {
            $this->info("No Users to Accept");
            return;
        }


        $index = 0;
        foreach ($unassignedBills as $i => $unassignedBill) {
            $user = &$users[$index];

            if ($user->totalSubmittedBills >= 3) {
                if ($index == 0) {
                    $outstandingCount = $unassignedBills->count() - $i;
                    $this->error("Not enough users to handle the bills outstanding ({$outstandingCount} remaining)");
                    return 1;
                }
            }

            $user['totalSubmittedBills'] += 1;
            $unassignedBill->users()->attach($user->id);
            $index++;
            if ($index >= $users->count()) {
                $index = 0;
            }
        }
    }
}
