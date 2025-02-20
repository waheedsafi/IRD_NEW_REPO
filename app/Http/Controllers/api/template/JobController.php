<?php

namespace App\Http\Controllers\api\template;

use App\Models\ModelJob;
use App\Models\Translate;
use App\Enums\LanguageEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Requests\template\job\JobStoreRequest;
use App\Models\ModelJobTrans;

class JobController extends Controller
{
    public function jobs()
    {
        $locale = App::getLocale();
        $tr = DB::table('model_jobs as mj')
            ->join('model_job_trans as mjt', function ($join) use ($locale) {
                $join->on('mjt.model_job_id', '=', 'mj.id')
                    ->where('mjt.language_name', $locale);
            })
            ->select('mj.id', "mjt.value as name", 'mj.created_at')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(JobStoreRequest $request)
    {
        $request->validated();
        // 1. Create
        $job = ModelJob::create([]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            ModelJobTrans::create([
                "value" => $request["{$name}"],
                "model_job_id" => $job->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else {
            $name = $request->pashto;
        }
        return response()->json([
            'message' => __('app_translation.success'),
            'job' => [
                "id" => $job->id,
                "name" => $name,
                "created_at" => $job->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function destroy($id)
    {
        $job = ModelJob::find($id);
        if ($job) {
            $job->delete();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
    }
    public function job($id)
    {
        $job = DB::table('model_job_trans as mjt')
            ->where('mjt.model_job_id', 4)
            ->select(
                'mjt.model_job_id',
                DB::raw("MAX(CASE WHEN mjt.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN mjt.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN mjt.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('mjt.model_job_id')
            ->first();
        return response()->json(
            [
                "id" => $job->model_job_id,
                "english" => $job->english,
                "farsi" => $job->farsi,
                "pashto" => $job->pashto,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function update(JobStoreRequest $request)
    {
        $request->validated();
        // This validation not exist in UrgencyStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        // 1. Find
        $job = ModelJob::find($request->id);
        if (!$job) {
            return response()->json([
                'message' => __('app_translation.job_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $trans = ModelJobTrans::where('model_job_id', $request->id)
            ->select('id', 'language_name', 'value')
            ->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $trans->where('language_name', $code)->first();
            $tran->value = $request["{$name}"];
            $tran->save();
        }

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else {
            $name = $request->pashto;
        }

        return response()->json([
            'message' => __('app_translation.success'),
            'job' => [
                "id" => $job->id,
                "name" => $name,
                "created_at" => $job->created_at
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
