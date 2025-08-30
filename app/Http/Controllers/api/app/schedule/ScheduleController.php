<?php

namespace App\Http\Controllers\api\app\schedule;

use App\Enums\Status\SchedualStatusEnum;
use App\Enums\Status\ScheduleStatusEnum;
use App\Models\Schedule;
use App\Models\ScheduleItem;
use Illuminate\Http\Request;
use App\Enums\Status\StatusEnum;
use App\Enums\Type\TaskTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB as FacadesDB;
use App\Http\Requests\app\schedule\ScheduleRequest;
use App\Http\Requests\app\schedule\ScheduleUpdateRequest;
use App\Models\Document as ModelsDocument;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Models\Document;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ScheduleDocument;
use App\Models\ScheduleItemStatus;
use Carbon\Carbon;

class ScheduleController extends Controller
{


    protected $pendingTaskRepository;
    protected $storageRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        StorageRepositoryInterface $storageRepository
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;

        $this->storageRepository = $storageRepository;
    }

    public function schedules(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $locale = App::getLocale();
        $request = DB::table('schedules as sch')
            ->where('sch.date', '>=', $startDate)
            ->where('sch.date', '<=', $endDate)
            ->join('schedule_status_trans as scht', function ($join) use ($locale) {
                $join->on('scht.schedule_status_id', 'sch.schedule_status_id')
                    ->where('scht.language_name', $locale);
            })
            ->select('sch.id', 'scht.value as status', 'sch.date')->get();
        return response()->json(
            $request
        );
    }
    public function prepareSchedule(Request $request)
    {
        $count = $request->count ?? 10;
        $ids = $request->input('ids');

        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            $ids = is_array($decoded) ? $decoded : [];
        }

        $locale = App::getLocale();

        // 1. Always fetch selected (special) projects by ID — regardless of their current status
        $projectsFromIds = collect();
        if (!empty($ids)) {
            $projectsFromIds = DB::table('projects as pro')
                ->join('project_trans as prot', function ($join) use ($locale) {
                    $join->on('prot.project_id', '=', 'pro.id')
                        ->where('prot.language_name', $locale);
                })
                ->whereIn('pro.id', $ids)
                ->select('pro.id', 'prot.name')
                ->get();
        }

        $fetchedCount = $projectsFromIds->count();
        $remainingCount = $count - $fetchedCount;

        $remainingProjects = collect();

        // 2. Fetch remaining from pending projects (excluding already included ones)
        if ($remainingCount > 0) {
            $remainingProjects = DB::table('projects as pro')
                ->join('project_statuses as pros', function ($join) {
                    $join->on('pro.id', '=', 'pros.project_id')
                        ->where('is_active', true);
                })
                ->join('project_trans as prot', function ($join) use ($locale) {
                    $join->on('prot.project_id', '=', 'pro.id')
                        ->where('prot.language_name', $locale);
                })
                ->where('pros.status_id', StatusEnum::pending_for_schedule->value)
                ->whereNotIn('pro.id', $projectsFromIds->pluck('id'))
                ->select('pro.id', 'prot.name')
                ->limit($remainingCount)
                ->get();
        }

        // 3. Merge both groups and return
        $projects = $projectsFromIds->merge($remainingProjects)->take($count);

        return response()->json($projects);
    }

    // public function prepareSchedule(Request $request)
    // {
    //     $count = $request->count ?? 10;
    //     $ids = $request->input('ids');
    //     if (is_string($ids)) {
    //         $decoded = json_decode($ids, true);
    //         $ids = is_array($decoded) ? $decoded : [];
    //     }
    //     $locale = App::getLocale();

    //     // 1. Get the projects by incoming ids (if any)
    //     $projectsFromIds = collect();
    //     if (!empty($ids)) {
    //         $projectsFromIds = DB::table('projects as pro')
    //             ->join('project_statuses as pros', function ($join) {
    //                 $join->on('pro.id', '=', 'pros.project_id')
    //                     ->where('is_active', true);
    //             })
    //             ->join('project_trans as prot', function ($join) use ($locale) {
    //                 $join->on('prot.project_id', '=', 'pro.id')
    //                     ->where('prot.language_name', $locale);
    //             })
    //             ->whereIn('pro.id', $ids)
    //             ->where('pros.status_id', StatusEnum::pending_for_schedule->value)
    //             ->select('pro.id', 'prot.name')
    //             ->get();
    //     }


    //     $fetchedCount = $projectsFromIds->count();
    //     $remainingCount = $count - $fetchedCount;


    //     $remainingProjects = collect();

    //     if ($remainingCount > 0) {
    //         $query = DB::table('projects as pro')
    //             ->join('project_statuses as pros', function ($join) {
    //                 $join->on('pro.id', '=', 'pros.project_id')
    //                     ->where('is_active', true);
    //             })->join('project_trans as prot', function ($join) use ($locale) {
    //                 $join->on('prot.project_id', '=', 'pro.id')
    //                     ->where('prot.language_name', $locale);
    //             })
    //             ->where('pros.status_id', StatusEnum::pending_for_schedule->value)
    //             ->select('pro.id', 'prot.name');

    //         if ($remainingCount != $count) {
    //             $query->whereNotIn('pro.id', $ids);
    //         }

    //         $remainingProjects = $query->limit($remainingCount)->get();
    //     }



    //     // 3. Merge both and return
    //     $projects = $projectsFromIds->merge($remainingProjects);

    //     return response()->json($projects);
    // }

    public function store(ScheduleRequest $request)
    {
        DB::beginTransaction();
        $authUser = $request->user();
        // Create Schedule
        $schedule = Schedule::create([
            'date' => Carbon::parse($request['date'])->toDateString(),
            'start_time' => $request['start_time'] ?? '08:00',
            'end_time' => $request['end_time'],
            'representators_count' => $request['presentation_count'],
            'presentation_lenght' => $request['presentation_length'],
            'gap_between' => $request['gap_between'],
            'lunch_start' => $request['lunch_start'],
            'lunch_end' => $request['lunch_end'],
            'dinner_start' => $request['dinner_start'],
            'dinner_end' => $request['dinner_end'],
            'presentation_before_lunch' => $request['presentation_count'] - $request['presentations_after_lunch'],
            'presentation_after_lunch' => $request['presentations_after_lunch'],
            'is_hour_24' => $request['is_hour_24'] ?? false,
            'schedule_status_id' => ScheduleStatusEnum::Scheduled->value
        ]);

        foreach ($request['scheduleItems'] as $item) {
            // 1. Add schedule item
            $scheduleItem  = ScheduleItem::create([
                'project_id' => $item['projectId'],
                'schedule_id' => $schedule->id,
                'start_time' => $item['slot']['presentation_start'],
                'end_time' => $item['slot']['presentation_end'],
            ]);

            // 2. Add schedule item document
            if (!empty($item['attachment'])) {
                $attachment = $item['attachment'];
                $pendingId = $attachment['pending_id'];
                $this->storageRepository->scheduleDocumentStore($schedule->id, $pendingId, function ($docData) use (&$scheduleItem, &$pendingId) {
                    $document = Document::create([
                        'actual_name' => $docData['actual_name'],
                        'size' => $docData['size'],
                        'path' => $docData['path'],
                        'type' => $docData['type'],
                        'check_list_id' => $docData['check_list_id'],
                    ]);

                    ScheduleDocument::create([
                        'document_id' => $document->id,
                        'schedule_item_id' => $scheduleItem->id,
                    ]);
                });
                $this->pendingTaskRepository->destroyPendingTaskById($pendingId);
            }
            // 3. Change project status
            ProjectStatus::where('project_id', $item['projectId'])->update(['is_active' => false]);
            ProjectStatus::create([
                'is_active' => true,
                'project_id' => $item['projectId'],
                'status_id' => StatusEnum::scheduled->value,
                'comment' => 'Schedule for the presentation',
                'userable_type' => $this->getModelName(get_class($authUser)),
                'userable_id' => $authUser->id,
            ]);
        }

        DB::commit();
        return response()->json(['message' => __('app_translation.success')], 200);
    }

    public function edit($id)
    {
        $locale = App::getLocale();

        // Fetch the schedule
        $schedule = DB::table('schedules')->where('id', $id)->first();

        if (!$schedule) {
            return response()->json(['message' => __('app_translation.not_found')], 404);
        }

        // Fetch schedule items with project names
        $scheduleItems = DB::table('schedule_items as schi')
            ->join('project_trans as prot', function ($join) use ($locale) {
                $join->on('schi.project_id', '=', 'prot.project_id')
                    ->where('prot.language_name', $locale);
            })
            ->where('schedule_id', $id)
            ->select('prot.name as project_name', 'schi.*')
            ->get();

        // Get all schedule item IDs
        $itemIds = $scheduleItems->pluck('id')->all();

        // Fetch all schedule documents for these items
        $scheduleDocuments = DB::table('schedule_documents')
            ->whereIn('schedule_item_id', $itemIds)
            ->get()
            ->keyBy('schedule_item_id');

        // Get all document IDs linked to these schedule documents
        $documentIds = $scheduleDocuments->pluck('document_id')->all();

        // Fetch all documents in one query
        $documents = DB::table('documents')
            ->whereIn('id', $documentIds)
            ->get()
            ->keyBy('id');

        $formattedItems = [];
        $projects = [];
        $specialProjects = [];

        foreach ($scheduleItems as $item) {
            $document = null;

            // Check if this schedule item has a document
            if (isset($scheduleDocuments[$item->id])) {
                $docId = $scheduleDocuments[$item->id]->document_id;

                if (isset($documents[$docId])) {
                    $doc = $documents[$docId];
                    $document = [
                        'document_id' => $doc->id,
                        'name' => $doc->actual_name,
                        'size' => $doc->size,
                        'check_list_id' => $doc->check_list_id,
                        'extension' => $doc->type,
                        'path' => $doc->path,
                    ];
                }
            }

            // Calculate gap_end
            $gapEnd = Carbon::parse($item->end_time)
                ->addMinutes($schedule->gap_between)
                ->format('H:i');

            // Format schedule item for response
            $formattedItems[] = [
                'slot' => [
                    'id' => $item->id,
                    'presentation_start' => $item->start_time,
                    'presentation_end' => $item->end_time,
                    'gap_end' => $gapEnd,
                ],
                'projectId' => $item->project_id,
                'project_name' => $item->project_name,
                'attachment' => $document,
                'selected' => false,
            ];

            // Build projects array (like React)
            $proj = [
                'id' => $item->project_id,
                'name' => $item->project_name,
                'attachment' => $document,
                'selected' => true,
            ];
            $projects[] = $proj;

            // Build special_projects for those with attachments
            if ($document) {
                $specialProjects[] = [
                    'project' => ['id' => $proj['id'], 'name' => $proj['name']],
                    'attachment' => $document,
                ];
            }
        }

        // Final data structure with projects & special_projects included
        $data = [
            'id' => $schedule->id,
            'date' => $schedule->date,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'dinner_start' => $schedule->dinner_start,
            'dinner_end' => $schedule->dinner_end,
            'gap_between' => $schedule->gap_between,
            'lunch_start' => $schedule->lunch_start,
            'lunch_end' => $schedule->lunch_end,
            'presentation_length' => $schedule->presentation_lenght,
            'presentation_count' => $schedule->representators_count,
            'presentations_after_lunch' => $schedule->presentation_after_lunch,
            'is_hour_24' => (bool) $schedule->is_hour_24,
            'scheduleItems' => $formattedItems,
            'projects' => $projects,
            'special_projects' => $specialProjects,
        ];

        return response()->json($data);
    }

    public function update(ScheduleRequest $request)
    {
        DB::beginTransaction();

        $authUser = $request->user();
        $id = $request['id'];

        // Fetch existing schedule manually
        $schedule = DB::table('schedules')->where('id', $id)->first();
        if (!$schedule) {
            return response()->json(['message' => __('app_translation.schedule_not_found')], 404);
        }

        // Update schedule details
        DB::table('schedules')->where('id', $id)->update([
            'date' => Carbon::parse($request['date'])->toDateString(),
            'start_time' => $request['start_time'] ?? '08:00',
            'end_time' => $request['end_time'],
            'representators_count' => $request['presentation_count'],
            'presentation_lenght' => $request['presentation_length'],
            'gap_between' => $request['gap_between'],
            'lunch_start' => $request['lunch_start'],
            'lunch_end' => $request['lunch_end'],
            'dinner_start' => $request['dinner_start'],
            'dinner_end' => $request['dinner_end'],
            'presentation_before_lunch' => $request['presentations_before_lunch'],
            'presentation_after_lunch' => $request['presentations_after_lunch'],
            'is_hour_24' => $request['is_hour_24'] ?? false,
        ]);

        // Retrieve existing schedule items
        $existingItems = DB::table('schedule_items')->where('schedule_id', $id)->get();
        $existingItemIds = $existingItems->pluck('id')->toArray();
        $oldProjectIds = $existingItems->pluck('project_id')->toArray();

        $newProjectIds = [];
        $receivedItemIds = [];

        foreach ($request['scheduleItems'] as $item) {
            $newProjectIds[] = $item['projectId'];

            // Update or insert schedule item
            if (!empty($item['slot']['id']) && in_array($item['slot']['id'], $existingItemIds)) {
                DB::table('schedule_items')->where('id', $item['slot']['id'])->update([
                    'project_id' => $item['projectId'],
                    'start_time' => $item['slot']['presentation_start'],
                    'end_time' => $item['slot']['presentation_end'],
                ]);
                $scheduleItemId = $item['slot']['id'];
            } else {
                $scheduleItemId = DB::table('schedule_items')->insertGetId([
                    'project_id' => $item['projectId'],
                    'schedule_id' => $id,
                    'start_time' => $item['slot']['presentation_start'],
                    'end_time' => $item['slot']['presentation_end'],
                ]);
            }

            $receivedItemIds[] = $scheduleItemId;

            // Handle attachment if present
            if (!empty($item['attachment'])) {
                $pendingId = $item['attachment']['pending_id'] ?? null;
                $existingScheduleDoc = DB::table('schedule_documents')
                    ->where('schedule_item_id', $scheduleItemId)
                    ->first();

                if ($pendingId) {
                    $this->storageRepository->scheduleDocumentStore($id, $pendingId, function ($docData) use ($scheduleItemId, $existingScheduleDoc) {
                        $docId = DB::table('documents')->insertGetId([
                            'actual_name' => $docData['actual_name'],
                            'size' => $docData['size'],
                            'path' => $docData['path'],
                            'type' => $docData['type'],
                            'check_list_id' => $docData['check_list_id'],
                        ]);

                        if ($existingScheduleDoc) {
                            DB::table('schedule_documents')
                                ->where('schedule_item_id', $scheduleItemId)
                                ->update(['document_id' => $docId]);
                        } else {
                            DB::table('schedule_documents')->insert([
                                'document_id' => $docId,
                                'schedule_item_id' => $scheduleItemId,
                            ]);
                        }
                    });

                    $this->pendingTaskRepository->destroyPendingTaskById($pendingId);
                }
            }

            // Update project status
            DB::table('project_statuses')->where('project_id', $item['projectId'])->update(['is_active' => false]);
            DB::table('project_statuses')->insert([
                'is_active' => true,
                'project_id' => $item['projectId'],
                'status_id' => StatusEnum::scheduled->value,
                'comment' => 'Schedule updated for the presentation',
                'userable_type' => $this->getModelName(get_class($authUser)),
                'userable_id' => $authUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Delete schedule items no longer included
        $itemsToDelete = array_diff($existingItemIds, $receivedItemIds);
        if (!empty($itemsToDelete)) {
            DB::table('schedule_items')->whereIn('id', $itemsToDelete)->delete();
        }

        // Remove schedule_documents entries only for removed projects
        $removedProjectIds = array_diff($oldProjectIds, $newProjectIds);
        if (!empty($removedProjectIds)) {
            // Get schedule_item IDs for removed projects
            $scheduleItemIdsToClean = DB::table('schedule_items')
                ->where('schedule_id', $id)
                ->whereIn('project_id', $removedProjectIds)
                ->pluck('id')
                ->toArray();

            if (!empty($scheduleItemIdsToClean)) {
                DB::table('schedule_documents')
                    ->whereIn('schedule_item_id', $scheduleItemIdsToClean)
                    ->delete();
            }

            // Update project statuses for removed projects
            foreach ($removedProjectIds as $pid) {
                DB::table('project_statuses')->where('project_id', $pid)->update(['is_active' => false]);
                DB::table('project_statuses')->insert([
                    'is_active' => true,
                    'project_id' => $pid,
                    'status_id' => StatusEnum::pending_for_schedule->value,
                    'comment' => 'Project removed from schedule',
                    'userable_type' => $this->getModelName(get_class($authUser)),
                    'userable_id' => $authUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::commit();

        return response()->json(['message' => __('app_translation.success')], 200);
    }


    // public function update(ScheduleRequest $request)
    // {
    //     DB::beginTransaction();
    //     $authUser = $request->user();

    //     $id = $request['id'];

    //     // Find schedule manually
    //     $schedule = DB::table('schedules')->where('id', $id)->first();
    //     if (!$schedule) {
    //         return response()->json(['message' => __('app_translation.schedule_not_found')], 404);
    //     }

    //     // Update schedule manually
    //     DB::table('schedules')->where('id', $id)->update([
    //         'date' => Carbon::parse($request['date'])->toDateString() ?? now()->toDateString(),
    //         'start_time' => $request['start_time'] ?? '08:00',
    //         'end_time' => $request['end_time'],
    //         'representators_count' => $request['presentation_count'],
    //         'presentation_lenght' => $request['presentation_length'],
    //         'gap_between' => $request['gap_between'],
    //         'lunch_start' => $request['lunch_start'],
    //         'lunch_end' => $request['lunch_end'],
    //         'dinner_start' => $request['dinner_start'],
    //         'dinner_end' => $request['dinner_end'],
    //         'presentation_before_lunch' => $request['presentations_before_lunch'],
    //         'presentation_after_lunch' => $request['presentations_after_lunch'],
    //         'is_hour_24' => $request['is_hour_24'] ?? false,
    //     ]);

    //     // Get old schedule items manually
    //     $existingScheduleItems = DB::table('schedule_items')->where('schedule_id', $id)->get();
    //     $existingItemIds = $existingScheduleItems->pluck('id')->toArray();
    //     $oldProjectIds = $existingScheduleItems->pluck('project_id')->toArray();

    //     $newProjectIds = [];
    //     $receivedItemIds = [];

    //     foreach ($request['scheduleItems'] as $item) {
    //         $newProjectIds[] = $item['projectId'];

    //         // Update or insert item
    //         if (!empty($item['slot']['id']) && in_array($item['slot']['id'], $existingItemIds)) {
    //             DB::table('schedule_items')->where('id', $item['slot']['id'])->update([
    //                 'project_id' => $item['projectId'],
    //                 'start_time' => $item['slot']['presentation_start'],
    //                 'end_time' => $item['slot']['presentation_end'],
    //             ]);
    //             $scheduleItemId = $item['slot']['id'];
    //         } else {
    //             $scheduleItemId = DB::table('schedule_items')->insertGetId([
    //                 'project_id' => $item['projectId'],
    //                 'schedule_id' => $id,
    //                 'start_time' => $item['slot']['presentation_start'],
    //                 'end_time' => $item['slot']['presentation_end'],
    //             ]);
    //         }

    //         $receivedItemIds[] = $scheduleItemId;

    //         // Handle attachments
    //         if (!empty($item['attachment'])) {
    //             $attachment = $item['attachment'];
    //             $pendingId = $attachment['pending_id'] ?? null;

    //             $existingScheduleDoc = DB::table('schedule_documents')->where('schedule_item_id', $scheduleItemId)->first();

    //             if ($pendingId) {
    //                 $this->storageRepository->scheduleDocumentStore($id, $pendingId, function ($docData) use ($scheduleItemId, $existingScheduleDoc) {
    //                     $documentId = DB::table('documents')->insertGetId([
    //                         'actual_name' => $docData['actual_name'],
    //                         'size' => $docData['size'],
    //                         'path' => $docData['path'],
    //                         'type' => $docData['type'],
    //                         'check_list_id' => $docData['check_list_id'],
    //                     ]);

    //                     if ($existingScheduleDoc) {
    //                         DB::table('schedule_documents')->where('schedule_item_id', $scheduleItemId)->update([
    //                             'document_id' => $documentId
    //                         ]);
    //                     } else {
    //                         DB::table('schedule_documents')->insert([
    //                             'document_id' => $documentId,
    //                             'schedule_item_id' => $scheduleItemId,
    //                         ]);
    //                     }
    //                 });

    //                 $this->pendingTaskRepository->destroyPendingTaskById($pendingId);
    //             }
    //         }

    //         // Update project status
    //         DB::table('project_statuses')->where('project_id', $item['projectId'])->update(['is_active' => false]);

    //         DB::table('project_statuses')->insert([
    //             'is_active' => true,
    //             'project_id' => $item['projectId'],
    //             'status_id' => StatusEnum::scheduled->value,
    //             'comment' => 'Schedule updated for the presentation',
    //             'userable_type' => $this->getModelName(get_class($authUser)),
    //             'userable_id' => $authUser->id,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     // Delete removed schedule items
    //     $itemsToDelete = array_diff($existingItemIds, $receivedItemIds);
    //     if (!empty($itemsToDelete)) {
    //         DB::table('schedule_items')->whereIn('id', $itemsToDelete)->delete();
    //         // You can also delete associated documents here if needed
    //     }

    //     // Restore status of removed projects
    //     $removedProjectIds = array_diff($oldProjectIds, $newProjectIds);

    //     foreach ($removedProjectIds as $removedProjectId) {
    //         DB::table('project_statuses')->where('project_id', $removedProjectId)->update(['is_active' => false]);

    //         DB::table('project_statuses')->insert([
    //             'is_active' => true,
    //             'project_id' => $removedProjectId,
    //             'status_id' => StatusEnum::pending_for_schedule->value,
    //             'comment' => 'Project removed from schedule',
    //             'userable_type' => $this->getModelName(get_class($authUser)),
    //             'userable_id' => $authUser->id,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }

    //     DB::commit();

    //     return response()->json(['message' => __('app_translation.success')], 200);
    // }
}
