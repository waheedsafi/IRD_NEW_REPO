<?php

namespace App\Http\Controllers\api\template\approval;

use App\Models\Ngo;
use App\Models\User;
use App\Models\Donor;
use App\Enums\Type\ApprovalTypeEnum;
use App\Http\Controllers\Controller;
use App\Repositories\Approval\ApprovalRepositoryInterface;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalRepository;
    public function __construct(
        ApprovalRepositoryInterface $approvalRepository
    ) {
        $this->approvalRepository = $approvalRepository;
    }
    // Ngo
    public function pendingNgoApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            Ngo::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedNgoApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            Ngo::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedNgoApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            Ngo::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    // User
    public function pendingUserApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            User::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedUserApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            User::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedUserApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            User::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    // Donor
    public function pendingDonorApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            Donor::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedDonorApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            Donor::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedDonorApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            Donor::class
        );
        $this->applySearch($query, $request);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        $allowedColumns = ['id', 'requester'];

        if ($searchColumn && $searchValue) {
            $allowedColumns = [
                'id' => 'a.id',
                'requester' => 'nt.name',
            ];
            // Ensure that the search column is allowed
            if (in_array($searchColumn, array_keys($allowedColumns))) {
                $query->where($allowedColumns[$searchColumn], 'like', '%' . $searchValue . '%');
            }
        }
    }
}
