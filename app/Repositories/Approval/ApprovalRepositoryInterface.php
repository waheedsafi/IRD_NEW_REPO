<?php

namespace App\Repositories\Approval;

interface ApprovalRepositoryInterface
{
    /**
     * Creates a approval.
     * 
     *
     * @param string requester_id
     * @param string requester_type
     * @param string notifier_type_id
     * @param string request_comment
     * @return App\Models\Approval
     */
    public function storeApproval($requester_id, $requester_type, $notifier_type_id, $request_comment);
    /**
     * Creates a approval document.
     * 
     *
     * @param string approval_id
     * @param string documentable_id
     * @param string documentable_type
     * @return App\Models\ApprovalDocument
     */
    public function storeApprovalDocument($approval_id, $documentable_id, $documentable_type);
}
