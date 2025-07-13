<?php

namespace App\Http\Controllers\api\app\projects;

use App\Enums\Status\StatusEnum;
use Illuminate\Http\Request;
use App\Enums\Type\TaskTypeEnum;
use App\Traits\Helper\FilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class ProjectController extends Controller
{
    use FilterTrait;
    protected $pendingTaskRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();

        $authUser = $request->user();
        $user_id = $authUser->id;

        $query = DB::table('projects as pro')
            ->where('pro.ngo_id', $user_id)
            ->join('project_trans as prot', function ($join) use ($locale) {
                $join->on('pro.id', '=', 'prot.project_id')
                    ->where('prot.language_name', $locale);
            })
            ->join('project_statuses as ps', function ($join) {
                $join->on('ps.project_id', '=', 'pro.id')
                    ->where('ps.is_active', true);
            })
            ->join('status_trans as st', function ($join)  use ($locale) {
                $join->on('st.status_id', '=', 'ps.status_id')
                    ->where('st.language_name', $locale);
            })
            ->join('donor_trans as dont', function ($join) use ($locale) {
                $join->on('dont.donor_id', 'pro.donor_id')
                    ->where('dont.language_name', $locale);
            })
            ->join('currency_trans as curt', function ($join) use ($locale) {
                $join->on('pro.currency_id', 'curt.currency_id')
                    ->where('curt.language_name', $locale);
            })
            ->select(
                'pro.id',
                'pro.total_budget as budget',
                'pro.start_date',
                'curt.name as currency',
                'pro.end_date',
                'pro.donor_registration_no',
                'prot.name as project_name',
                'dont.name as donor',
                'st.name as status',
                'ps.status_id',
                'pro.created_at'
            );
        $this->applyDate($query, $request, 'pro.created_at', 'pro.created_at');
        $allowColumn = [
            'title' => 'prot.title',
            'donor' => 'dont.donor'
        ];
        $this->applyFilters($query, $request, $allowColumn);

        $this->applySearch($query, $request, $allowColumn);

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'projects' => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function startRegisterForm(Request $request, $ngo_id)
    {
        $locale = App::getLocale();

        $pendingTaskContent = $this->pendingTaskRepository->pendingTask($request, TaskTypeEnum::project_registeration->value, $ngo_id);
        if ($pendingTaskContent['content']) {
            return response()->json([
                'message' => __('app_translation.success'),
                'content' => $pendingTaskContent['content']
            ], 200);
        }

        return response()->json([
            [],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function destroyPendingTask(Request $request, $id)
    {
        $request->validate([
            'task_type' => "required"
        ]);
        $authUser = $request->user();
        $task_type = $request->task_type;

        $this->pendingTaskRepository->destroyPendingTask(
            $authUser,
            $task_type,
            $id
        );

        return response()->json([
            "message" => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function ngoProjects(Request $request)
    {
        $authNgo = $request->user();

        $result = DB::table('projects')
            ->leftJoin('project_details', 'projects.id', '=', 'project_details.project_id')
            ->where('projects.ngo_id', $authNgo->id)
            ->select(
                DB::raw('COUNT(DISTINCT projects.id) as total_projects'),
                DB::raw('COALESCE(SUM(project_details.budget), 0) as total_budget'),
                DB::raw('COALESCE(SUM(project_details.direct_beneficiaries), 0) as total_direct_beneficiaries'),
                DB::raw('COALESCE(SUM(project_details.in_direct_beneficiaries), 0) as total_in_direct_beneficiaries')
            )
            ->first();

        return response()->json([
            'counts' => [
                'total_projects' => $result->total_projects,
                'total_budget' => $result->total_budget,
                'total_direct_beneficiaries' => $result->total_direct_beneficiaries,
                'total_in_direct_beneficiaries' => $result->total_in_direct_beneficiaries,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function projectsWithName()
    {
        $locale = App::getLocale();
        $result = DB::table('projects as p')
            ->join(
                'project_trans as pt',
                function ($join) use ($locale) {
                    $join->on('p.id', '=', 'pt.project_id')
                        ->where('pt.language_name', $locale);
                }
            )
            ->join('project_statuses as ps', function ($join) use ($locale) {
                $join->on('p.id', '=', 'ps.project_id')
                    ->where('ps.is_active', true);
            })
            ->whereIn('ps.status_id', [StatusEnum::has_comment->value, StatusEnum::pending_for_schedule->value])
            ->select(
                'p.id',
                'pt.name',
            )
            ->get();

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
