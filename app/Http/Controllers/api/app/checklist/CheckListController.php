<?php

namespace App\Http\Controllers\api\app\checklist;

use App\Enums\CheckListEnum;
use App\Enums\CheckListTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CheckListController extends Controller
{
    public function ngoRegister()
    {
        $locale = App::getLocale();
        $tr = DB::table('check_lists as cl')
            ->where('cl.active', true)
            ->where('cl.check_list_type_id', CheckListTypeEnum::ngoRegister->value)
            ->where('cl.id', '!=', CheckListEnum::director_work_permit->value)
            ->join('check_list_trans as clt', 'clt.check_list_id', '=', 'cl.id')
            ->where('clt.language_name', $locale)
            ->select(
                'clt.value as name',
                'cl.id',
                'cl.acceptable_mimes',
                'cl.acceptable_extensions',
                'cl.description'
            )
            ->orderBy('cl.id')
            ->get();
        return response()->json([
            'checklist' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function ngoRegisterAbroadDirector()
    {
        $locale = App::getLocale();
        $tr = DB::table('check_lists as cl')
            ->where('cl.active', true)
            ->where('cl.check_list_type_id', CheckListTypeEnum::ngoRegister->value)
            ->join('check_list_trans as clt', 'clt.check_list_id', '=', 'cl.id')
            ->where('clt.language_name', $locale)
            ->select(
                'clt.value as name',
                'cl.id',
                'cl.acceptable_mimes',
                'cl.acceptable_extensions',
                'cl.description'
            )
            ->orderBy('cl.id')
            ->get();
        return response()->json([
            'checklist' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function projectRegister()
    {
        $tr =  [];

        return response()->json([
            'ngos' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(Request $request)
    {
        $locale = App::getLocale();
        $tr =  [];
        return response()->json([
            'ngos' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(Request $request)
    {
        $locale = App::getLocale();
        $tr =  [];
        return response()->json([
            'ngos' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function destroy($id)
    {
        $locale = App::getLocale();
        $tr =  [];
        return response()->json([
            'ngos' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function checklists()
    {
        $locale = App::getLocale();
        $tr = DB::table('check_lists as cl')
            ->join('users as u', 'u.id', '=', 'cl.user_id')
            ->join('check_list_trans as clt', 'clt.check_list_id', '=', 'cl.id')
            ->where('clt.language_name', $locale)
            ->join('check_list_types as cltt', 'cltt.id', '=', 'cl.check_list_type_id')
            ->join('check_list_type_trans as clttt', 'clttt.check_list_type_id', '=', 'cltt.id')
            ->where('clttt.language_name', $locale)
            ->select(
                'clt.value as name',
                'cl.id',
                'cl.acceptable_mimes',
                'cl.acceptable_extensions',
                'cl.description',
                'cl.active',
                'clttt.value as type',
                'cltt.id as type_id',
                'u.username as saved_by',
                'cl.created_at'
            )
            ->orderBy('cltt.id')
            ->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function checklist($id)
    {
        $locale = App::getLocale();
        $checklist = DB::table('check_lists as cl')
            ->where('cl.id', $id)
            ->join('check_list_trans as clt', 'clt.check_list_id', '=', 'cl.id')
            ->where('clt.language_name', $locale)
            ->join('check_list_types as cltt', 'cltt.id', '=', 'cl.check_list_type_id')
            ->join('check_list_type_trans as clttt', 'clttt.check_list_type_id', '=', 'cltt.id')
            ->where('clttt.language_name', $locale)
            ->select(
                'clt.value as name',
                'cl.id',
                'cl.acceptable_mimes',
                'cl.acceptable_extensions',
                'cl.description',
                'cl.active',
                'cl.file_size',
                'clttt.value as type',
                'clttt.id as type_id',
            )
            ->orderBy('cltt.id')
            ->first();

        // Post-process the result
        $checklist->acceptable_mimes = explode(',', $checklist->acceptable_mimes);
        $checklist->acceptable_extensions = explode(',', $checklist->acceptable_extensions);
        return $checklist;
    }
    public function checklistTypes()
    {
        $locale = App::getLocale();
        $tr =  DB::table('check_list_types as clt')
            ->join('check_list_type_trans as cltt', 'cltt.check_list_type_id', '=', 'clt.id')
            ->where('cltt.language_name', $locale)
            ->select(
                'cltt.value as name',
                'clt.id',
            )
            ->orderBy('clt.id')
            ->get();

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
