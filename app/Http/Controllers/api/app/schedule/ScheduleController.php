<?php

namespace App\Http\Controllers\api\app\schedule;

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
use App\Models\Document as ModelsDocument;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Models\Document;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\ScheduleDocument;
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


        $locale = App::getLocale();
        $request = DB::table('schedules as sch')
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

        // Decode the string '[1,2]' → array [1, 2]

        $ids = $request->input('ids');
        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            $ids = is_array($decoded) ? $decoded : [];
        }


        $locale = App::getLocale();

        // 1. Get the projects by incoming ids (if any)
        $projectsFromIds = collect();
        if (!empty($ids)) {
            $projectsFromIds = DB::table('projects as pro')
                ->join('project_statuses as pros', function ($join) {
                    $join->on('pro.id', '=', 'pros.project_id')
                        ->where('is_active', true);
                })
                ->join('project_trans as prot', function ($join) use ($locale) {
                    $join->on('prot.project_id', '=', 'pro.id')
                        ->where('prot.language_name', $locale);
                })
                ->whereIn('pro.id', $ids)
                ->where('pros.status_id', StatusEnum::pending_for_schedule->value)
                ->select('pro.id', 'prot.name')
                ->get();
        }


        $fetchedCount = $projectsFromIds->count();
        $remainingCount = $count - $fetchedCount;


        $remainingProjects = collect();

        if ($remainingCount > 0) {
            $query = DB::table('projects as pro')
                ->join('project_statuses as pros', function ($join) {
                    $join->on('pro.id', '=', 'pros.project_id')
                        ->where('is_active', true);
                })->join('project_trans as prot', function ($join) use ($locale) {
                    $join->on('prot.project_id', '=', 'pro.id')
                        ->where('prot.language_name', $locale);
                })
                ->where('pros.status_id', StatusEnum::pending_for_schedule->value)
                ->select('pro.id', 'prot.name');

            if ($remainingCount != $count) {
                $query->whereNotIn('pro.id', $ids);
            }

            $remainingProjects = $query->limit($remainingCount)->get();
        }



        // 3. Merge both and return
        $projects = $projectsFromIds->merge($remainingProjects);

        return response()->json($projects);
    }

    public function store(ScheduleRequest $request)
    {


        Log::info("messag:" . $request);
        $authUser = $request->user();
        DB::beginTransaction();

        // Insert into schedules table
        $schedule = Schedule::create([
            'date' => Carbon::parse($request['date'])->toDateString() ?? now()->toDateString(),
            'start_time' => $request['start_time'] ?? '08:00', // hardcoded or passed separately
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
            'is_hour_24' => true,
            'schedule_status_id' => 1 // change according to your statuses
        ]);

        foreach ($request['scheduleItems'] as $item) {
            $scheduleItem =   ScheduleItem::create([
                'project_id' => $item['projectId'],
                'schedule_id' => $schedule->id,
                'start_time' => $item['slot']['presentation_start'],
                'end_time' => $item['slot']['presentation_end'],

            ]);
            $updatedCount = ProjectStatus::where('project_id', $item['projectId'])
                ->update(['is_active' => false]);

            ProjectStatus::create([
                'is_active' => true,
                'project_id' => $item['projectId'],
                'status_id' => StatusEnum::scheduled->value,
                'comment' => 'Schedule for the persention',
                'userable_type' => $authUser->role_id,
                'userable_id' => $authUser->id

            ]);


            if (isset($item['attachment'])) {
                $attachment = $item['attachment'];
                $directorDocumentsId = [];
                $document =  $this->storageRepository->scheduleDocumentStore($schedule->id,  $attachment['pending_id'], function ($documentData) use (&$directorDocumentsId, $scheduleItem) {
                    $checklist_id = $documentData['check_list_id'];
                    $document = Document::create([
                        'actual_name' => $documentData['actual_name'],
                        'size' => $documentData['size'],
                        'path' => $documentData['path'],
                        'type' => $documentData['type'],
                        'check_list_id' => $checklist_id,
                    ]);

                    array_push($directorDocumentsId, $document->id);


                    ScheduleDocument::create([
                        'document_id' => $document->id,
                        'schedule_item_id' => $scheduleItem->id,
                    ]);
                });
                $this->pendingTaskRepository->destroyPendingTaskById(
                    $attachment['pending_id']
                );
            }
        }


        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),

        ], 200);
    }

    public function edit($id)
    {
        // Fetch the schedule
        $schedule = DB::table('schedules')->where('id', $id)->first();

        if (!$schedule) {
            return response()->json(['message' => __('app_translation.not_found')], 404);
        }

        // Fetch schedule items
        $scheduleItems = DB::table('schedule_items')
            ->where('schedule_id', $id)
            ->get();

        $formattedItems = [];

        foreach ($scheduleItems as $item) {
            // Fetch the related document (if exists)
            $scheduleDocument = DB::table('schedule_documents')
                ->where('schedule_item_id', $item->id)
                ->first();

            $document = null;
            if ($scheduleDocument) {
                $doc = DB::table('documents')->where('id', $scheduleDocument->document_id)->first();
                if ($doc) {
                    $document = [
                        'pending_id' => $doc->id, // You may store actual pending_id elsewhere
                        'name' => $doc->actual_name,
                        'size' => $doc->size,
                        'check_list_id' => $doc->check_list_id,
                        'extension' => $doc->type,
                        'path' => $doc->path
                    ];
                }
            }

            // Calculate gap_end
            $gapEnd = Carbon::parse($item->end_time)
                ->addMinutes($schedule->gap_between)
                ->format('H:i');

            $formattedItems[] = [
                'slot' => [
                    'id' => $item->id,
                    'presentation_start' => $item->start_time,
                    'presentation_end' => $item->end_time,
                    'gap_end' => $gapEnd
                ],
                'projectId' => $item->project_id,
                'attachment' => $document,
                'selected' => false
            ];
        }

        // Final data structure
        $data = [
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
            'scheduleItems' => $formattedItems
        ];

        return response()->json($data);
    }
}
