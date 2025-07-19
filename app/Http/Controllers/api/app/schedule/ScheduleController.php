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
use App\Models\ScheduleDocument;

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
                ->join('project_statuses as pros', 'pro.id', '=', 'pros.project_id')
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
                ->join('project_statuses as pros', 'pro.id', '=', 'pros.project_id')
                ->join('project_trans as prot', function ($join) use ($locale) {
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


        // Log::info("messag:" . $request);
        $authUser = $request->user();
        DB::beginTransaction();

        // Insert into schedules table
        $schedule = Schedule::create([
            'date' => now()->toDateString(), // or pass a real date if available
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
                'start_time' => $item['slot']['presentation_start'],
                'end_time' => $item['slot']['presentation_end']
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

        return 'success';
    }
}
