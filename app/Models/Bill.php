<?php

namespace App\Models;

use App\Enums\BillStageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bill extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'bill_reference',
        'bill_date',
        'submitted_at',
        'approved_at',
        'on_hold_at',
        'bill_stage_id',
    ];

    public static function fetchBillSummary()
    {
        return Bill::query()
            ->join("bill_stages as bs", "bs.id", "=", "bills.bill_stage_id")
            ->leftJoin("bill_user as bu", "bu.bill_id", "=", "bills.id")
            ->leftJoin("users as users", 'users.id', '=', 'bu.user_id')
            ->groupBy("users.id")
            ->select(["users.id"])
            ->selectRaw("coalesce(users.name,'Unassigned') as 'name'")
            ->selectRaw("count(distinct bills.id) as 'totalBills'")
            ->selectRaw("count(distinct if(bs.label=?,bills.id,null)) as 'totalSubmittedBills'", [BillStageEnum::SUBMITTED])
            ->selectRaw("count(distinct if(bs.label=?,bills.id,null)) as 'totalOnHoldBills'", [BillStageEnum::ON_HOLD])
            ->selectRaw("count(distinct if(bs.label=?,bills.id,null)) as 'totalApprovedBills'", [BillStageEnum::APPROVED])
            ->orderBy("name", 'asc')
            ->get();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function billStage(): BelongsTo
    {
        return $this->belongsTo(BillStage::class);
    }
}
