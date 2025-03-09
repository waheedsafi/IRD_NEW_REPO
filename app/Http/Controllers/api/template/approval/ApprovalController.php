<?php

namespace App\Http\Controllers\api\template\approval;

use App\Http\Controllers\Controller;
use App\Repositories\Approval\ApprovalRepositoryInterface;

class ApprovalController extends Controller
{
    protected $approvalRepository;
    public function __construct(
        ApprovalRepositoryInterface $approvalRepository
    ) {
        $this->approvalRepository = $approvalRepository;
    }
    public function approvals() {}
}
