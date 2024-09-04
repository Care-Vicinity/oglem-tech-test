<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Console\Command;

class AssignBillsToUsers extends Command {
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
    public function handle() {
        $unassignedBills = Bill::query()
            ->leftJoin("bill_user as bu", "bu.bill_id", "=", "bills.id")
            ->leftJoin("bill_stages as bs", "bs.id", "=", "bills.bill_stage_id")
            ->whereRaw("bu.id is null and bs.label='Submitted'")
            ->select(['bills.id'])
            ->get();

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
            ->selectRaw("count(distinct if(bs.label='Submitted',b.id,null)) as 'totalSubmittedBills'")
            ->orderByRaw('totalSubmittedBills asc, users.name asc')
            ->havingRaw('totalSubmittedBills < 3')
            ->get();

        if ($users->count() <= 0) {
            $this->info("No Users to Accept");
            return;
        }

        $index = 0;
        foreach ($unassignedBills as $i => $row) {
            $user = &$users[$index++];
            if ($index >= $users->count()) {
                $index = 0;
            }

            if ($user->totalSubmittedBills >= 3) {
                $outstandingCount = $unassignedBills->count() - $i;
                $this->error("Not enough users to handle the bills outstanding ({$outstandingCount} remaining)");
                return 1;
            }
            $user['totalSubmittedBills'] += 1;
            $row->users()->attach($user->id);
        }
    }
}
