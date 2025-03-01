<?php

namespace App\Http\Controllers\api\app\representor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\CheckList\CheckListEnum;
use App\Enums\Type\RepresentorTypeEnum;
use App\Http\Requests\app\representor\StoreRepresentorRequest;
use App\Http\Requests\app\representor\UpdateRepresentorRequest;

class RepresentorController extends Controller
{
    public function ngoRepresentor(Request $request, $id)
    {
        $locale = App::getLocale();


        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function ngoRepresentors($ngo_id)
    {
        $locale = App::getLocale();
        $representor = DB::table('agreements as a')
            ->where('a.ngo_id', $ngo_id)
            ->where('a.end_date', null)
            ->join('representers as r', function ($join) {
                $join->on('a.id', '=', 'r.represented_id');
            })
            ->where('r.type', RepresentorTypeEnum::ngo->value)
            ->join('representer_trans as rt', function ($join) use ($locale) {
                $join->on('r.id', '=', 'rt.representer_id')
                    ->where('rt.language_name', $locale);
            })
            ->join('users as u', 'r.user_id', '=', 'u.id')
            ->select(
                'r.id',
                'r.is_active',
                'r.created_at',
                'rt.full_name',
                'u.username',
                'a.id as agreement_id',
                'a.agreement_no',
                'a.start_date',
                'a.end_date',
                "u.username as saved_by"
            )
            ->get();

        return response()->json(
            $representor,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function store(StoreRepresentorRequest $request)
    {
        $request->validated();
        $id = $request->id;
        // 1. Get NGo

        // 2. Transaction
        DB::beginTransaction();
        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(UpdateRepresentorRequest $request)
    {
        $request->validated();
        $id = $request->id;

        DB::beginTransaction();

        DB::commit();

        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
