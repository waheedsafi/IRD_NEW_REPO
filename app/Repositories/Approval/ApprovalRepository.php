<?php

namespace App\Repositories\Approval;

use Carbon\Carbon;
use App\Models\Approval;
use App\Models\ApprovalDocument;

class ApprovalRepository implements ApprovalRepositoryInterface
{
    
    public function storeApproval($requester_id, $requester_type, $notifier_type_id, $request_comment)
    {
        return Approval::create([
            "request_comment" => $request_comment,
            "requester_id" => $requester_id,
            "request_date" => Carbon::now(),
            "requester_type" => $requester_type,
            "notifier_type_id" => $notifier_type_id,
        ]);
    }
    public function storeApprovalDocument($approval_id, $documentable_id, $documentable_type)
    {
        return ApprovalDocument::create([
            "approval_id" => $approval_id,
            "documentable_id" => $documentable_id,
            "documentable_type" => $documentable_type,
        ]);
    }
}
