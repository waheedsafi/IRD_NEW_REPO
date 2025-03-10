<?php

namespace App\Repositories\Approval;

use App\Models\Ngo;
use App\Models\Approval;
use App\Models\ApprovalDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Enums\Type\ApprovalTypeEnum;

class ApprovalRepository implements ApprovalRepositoryInterface
{

    public function getByNotifierTypeAndRequesterType($approval_type_id, $requester_type)
    {
        $locale = App::getLocale();

        return DB::table('approvals as a')
            ->where("a.requester_type", $requester_type)
            ->where("a.approval_type_id", $approval_type_id)
            ->join('approval_type_trans as att', function ($join) use ($locale) {
                $join->on('att.approval_type_id', '=', 'a.approval_type_id')
                    ->where('att.language_name', $locale);
            })
            ->join('ngo_trans as nt', function ($join) use ($locale) {
                $join->on('nt.ngo_id', '=', 'a.requester_id')
                    ->where('nt.language_name', $locale);
            })
            ->join('notifier_type_trans as ntt', function ($join) use ($locale) {
                $join->on('ntt.notifier_type_id', '=', 'a.notifier_type_id')
                    ->where('ntt.language_name', $locale);
            })
            ->select(
                'a.id',
                'a.request_date',
                'a.requester_id',
                'a.responder_id',
                'a.responder_type',
                'a.notifier_type_id',
                'ntt.value as notifier_type',
                'nt.name as requester',
                DB::raw('(
                    SELECT COUNT(*)
                    FROM approval_documents as ad_count
                    WHERE ad_count.approval_id = a.id
                ) as document_count')
            )
            ->get();
    }
    public function storeApproval($requester_id, $requester_type, $notifier_type_id, $request_comment)
    {
        return Approval::create([
            "request_comment" => $request_comment,
            "requester_id" => $requester_id,
            "approval_type_id" => ApprovalTypeEnum::pending->value,
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
