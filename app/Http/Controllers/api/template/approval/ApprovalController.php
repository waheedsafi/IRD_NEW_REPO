<?php

namespace App\Http\Controllers\api\template\approval;

use App\Models\Ngo;
use App\Models\User;
use App\Models\Donor;
use App\Enums\Type\ApprovalTypeEnum;
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
    // Ngo
    public function pendingNgoApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            Ngo::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedNgoApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            Ngo::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedNgoApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            Ngo::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    // User
    public function pendingUserApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            User::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedUserApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            User::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedUserApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            User::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    // Donor
    public function pendingDonorApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            Donor::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedDonorApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            Donor::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedDonorApproval()
    {
        $approvals = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            Donor::class
        );
        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
