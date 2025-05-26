<?php

namespace App\Http\Controllers\api\template;

use Illuminate\Http\Request;
use App\Enums\Status\StatusEnum;
use App\Enums\Type\StatusTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

use App\Http\Controllers\Controller;
use function Laravel\Prompts\select;

class StatusController extends Controller
{

    public function ngoBlockStatuses()
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
    public function blockStatusesType()
    {
        $locale = App::getLocale();
        $includes = [StatusEnum::block->value];
        $statusesType = DB::table('status_types as st')
            ->whereIn('st.id', $includes)
            ->leftjoin('status_type_trans as stt', function ($join) use ($locale) {
                $join->on('stt.status_type_id', '=', 'st.id')
                    ->where('stt.language_name', $locale);
            })
            ->select('st.id', 'stt.name')->get();

        return response()->json($statusesType);
    }
}
