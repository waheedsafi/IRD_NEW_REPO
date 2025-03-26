<?php

namespace App\Http\Controllers\api\template\task;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class PendingTaskController extends Controller
{
    protected $pendingTaskRepository;

    public function __construct(PendingTaskRepositoryInterface $pendingTaskRepository)
    {
        $this->pendingTaskRepository = $pendingTaskRepository;
    }
    public function storeWithContent(Request $request, $id)
    {
        $request->validate([
            'contents' => 'required|string',
            'step' => 'required|string',
            'task_type' => 'required|string',
        ]);

        $authUser = $request->user();
        $task = $this->pendingTaskRepository->storeTask(
            $authUser,
            $request->task_type,
            $id
        );

        $this->pendingTaskRepository->storeTaskContent(
            $task,
            $request->step,
            $request->contents
        );
        return response()->json(
            [
                'message' => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
