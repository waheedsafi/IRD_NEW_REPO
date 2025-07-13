<?php

namespace App\Http\Controllers\api\app\schedule;

use Illuminate\Http\Request;
use App\Enums\Status\StatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB as FacadesDB;

class ScheduleController extends Controller
{
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
}
