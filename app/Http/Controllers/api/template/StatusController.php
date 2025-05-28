<?php

namespace App\Http\Controllers\api\template;

use App\Models\NgoStatus;
use Illuminate\Http\Request;
use App\Enums\Status\StatusEnum;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class StatusController extends Controller
{

    public function ngoStatusTypes()
    {
        $locale = App::getLocale();
        $includes = [StatusEnum::block->value];
        $statuses = DB::table('statuses as st')
            ->whereIn('st.id', $includes)
            ->leftjoin('status_trans as stt', function ($join) use ($locale) {
                $join->on('stt.status_id', '=', 'st.id')
                    ->where('stt.language_name', $locale);
            })
            ->select('st.id', 'stt.name')->get();

        return response()->json($statuses);
    }
    public function changeNgoStatus(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'ngo_id' => 'required|integer',
            'status_id' => 'required|integer',
            'comment' => 'required|string',
        ]);

        $authUser = $request->user();

        // Fetch the currently active status for this NGO
        $previousStatus = NgoStatus::where('ngo_id', $validatedData['ngo_id'])
            ->where('is_active', true)
            ->first();

        // Check if the current active status allows transition
        if (
            $previousStatus &&
            ($previousStatus->status_id === StatusEnum::active->value ||
                $previousStatus->status_id === StatusEnum::block->value)
        ) {

            // Deactivate the old status
            $previousStatus->is_active = false;
            $previousStatus->save();

            // Create a new status entry
            $newStatus = NgoStatus::create([
                'status_id' => $validatedData['status_id'],
                'ngo_id' => $validatedData['ngo_id'],
                'comment' => $validatedData['comment'],
                'is_active' => true,
                'userable_id' => $authUser->id,
                'userable_type' => $this->getModelName(get_class($authUser)),
            ]);

            // Prepare response
            $data = [
                'ngo_status_id' => $newStatus->id,
                'is_active' => true,
                'created_at' => $newStatus->created_at,
            ];

            return response()->json([
                'message' => __('app_translation.success'),
                'status' => $data
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // Not authorized to change status
            return response()->json([
                'message' => __('app_translation.unauthorized')
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function ngoStatuses($id)
    {
        $locale = App::getLocale();
        $result = DB::table('ngos as n')
            ->where('n.id', '=', $id)
            ->join('ngo_statuses as ngs', function ($join) {
                $join->on('ngs.ngo_id', '=', 'n.id');
                // ->where('ngs.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ngs.status_id')
                    ->where('st.language_name', $locale);
            })->select(
                'n.id as ngo_id',
                'ngs.id',
                'ngs.comment',
                'ngs.status_id',
                'st.name',
                'ngs.userable_type',
                'ngs.is_active',
                'ngs.created_at',
            )->get();

        return response()->json([
            'statuses' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function agreementStatuses($id)
    {
        $locale = App::getLocale();
        $result = DB::table('ngos as n')
            ->where('n.id', '=', $id)
            ->join('agreements as a', function ($join) {
                $join->on('a.ngo_id', '=', 'n.id')
                    ->whereRaw('a.id = (select max(ns2.id) from agreements as ns2 where ns2.ngo_id = n.id)');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'a.id')
                    ->where('ags.is_active', true);
            })
            ->join('agreement_status_trans as ast', function ($join) use ($locale) {
                $join->on('ast.agreement_status_id', '=', 'ags.id')
                    ->where('ast.language_name', $locale);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ags.status_id')
                    ->where('st.language_name', $locale);
            })->select(
                'n.id as ngo_id',
                'ags.id',
                'ast.comment',
                'ags.status_id',
                'st.name',
                'ags.userable_type',
                'ags.is_active',
                'ags.created_at',
            )->get();

        return response()->json([
            'statuses' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
