<?php

namespace App\Http\Controllers\api\app\projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class ProjectManagerController extends Controller
{
    public function names($ngoId)
    {
        $locale = App::getLocale();
        $query =    DB::table('project_managers as pm')
            ->where('pm.ngo_id', $ngoId)
            ->join('project_manager_trans as pmt', function ($join) use ($locale) {
                $join->on('pm.id', '=', 'pmt.project_id')
                    ->where('language_name', $locale);
            })
            ->select(
                'pm.id',
                'pmt.fullname as name'
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
