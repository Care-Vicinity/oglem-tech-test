<?php

namespace App\Enums;

enum BillStageEnum: string
{
	case DRAFT = 'Draft';
	case SUBMITTED = 'Submitted';
	case APPROVED = 'Approved';
	case PAYING = 'Paying';
	case ON_HOLD = 'On Hold';
	case REJECTED = 'Rejected';
	case PAID = 'Paid';
}
