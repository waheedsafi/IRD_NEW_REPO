<?php

namespace App\Http\Controllers\api\template;

use Exception;
use App\Models\Translate;
use App\Enums\LanguageEnum;
use App\Models\Destination;
use App\Models\DestinationType;
use App\Enums\DestinationTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\template\destination\DestinationStoreRequest;
use App\Models\DestinationTrans;

use function PHPSTORM_META\map;

class DestinationController extends Controller
{
    public function destinations()
    {
        $locale = App::getLocale();

        $tr = DB::table('destinations as d')
            ->join('destination_trans as dt', function ($join) use ($locale) {
                $join->on('dt.destination_id', '=', 'd.id')
                    ->where('dt.language_name', $locale);
            })
            ->select('d.id', "dt.value as name")->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function completeDestinations()
    {
        $locale = App::getLocale();

        $tr = DB::table('destinations as d')
            ->leftJoin('destination_trans as dt', function ($join) use ($locale) {
                $join->on('dt.destination_id', '=', 'd.id')
                    ->where('dt.language_name', $locale);
            })
            ->leftJoin('destination_type_trans as dtt', function ($join) use ($locale) {
                $join->on('dtt.destination_type_id', '=', 'd.destination_type_id')
                    ->where('dtt.language_name', $locale);
            })
            ->select('d.id', "dt.value as name", 'd.color', 'd.destination_type_id', 'dtt.value as type', 'd.created_at')->get();

        $tr = $tr->map(function ($destination) {

            return [
                "id" => $destination->id,
                "name" => $destination->name,
                "color" => $destination->color,
                "type" => ["id" => $destination->destination_type_id, "name" => $destination->type],
                "created_at" => $destination->created_at,
            ];
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function directorates()
    {
        $locale = App::getLocale();
        $tr = DB::table('destinations as d')
            ->where('d.destination_type_id', DestinationTypeEnum::directorate->value)
            ->join('destination_trans as dt', function ($join) use ($locale) {
                $join->on('dt.destination_id', '=', 'd.id')
                    ->where('dt.language_name', $locale);
            })->select('d.id', "dt.value as name")->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function muqams()
    {
        $locale = App::getLocale();
        $tr = DB::table('destinations as d')
            ->where('d.destination_type_id', DestinationTypeEnum::muqam->value)
            ->join('destination_trans as dt', function ($join) use ($locale) {
                $join->on('dt.destination_id', '=', 'd.id')
                    ->where('dt.language_name', $locale);
            })->select('d.id', "dt.value as name")->get();

        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function destination($id)
    {
        $locale = App::getLocale();

        $tr = DB::table('destinations as d')
            ->where('d.id', $id)
            ->joinSub(function ($query) {
                $query->from('destination_trans as dt')
                    ->select(
                        'destination_id',
                        DB::raw("MAX(CASE WHEN language_name = 'fa' THEN value END) as farsi"),
                        DB::raw("MAX(CASE WHEN language_name = 'en' THEN value END) as english"),
                        DB::raw("MAX(CASE WHEN language_name = 'ps' THEN value END) as pashto")
                    )
                    ->groupBy('destination_id');
            }, 'dt', 'dt.destination_id', '=', 'd.id')
            // Join for the destination type translation
            ->leftJoin('destination_type_trans as dtt', function ($join) use ($locale) {
                $join->on('dtt.destination_type_id', '=', 'd.destination_type_id')
                    ->where('dtt.language_name', $locale);
            })
            ->select(
                'd.id',
                'dt.english',
                'dt.farsi',
                'dt.pashto',
                'd.color',
                'd.destination_type_id',
                'dtt.value as type',
                'd.created_at'
            )
            ->first();

        return response()->json([
            "id" => $tr->id,
            "english" => $tr->english,
            "farsi" => $tr->farsi,
            "pashto" => $tr->pashto,
            "color" => $tr->color,
            "type" => ["id" => $tr->destination_type_id, "name" => $tr->type],
            "created_at" => $tr->created_at,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(DestinationStoreRequest $request)
    {
        $request->validated();
        $destinationType = DestinationType::find($request->destination_type_id);
        if (!$destinationType) {
            return response()->json([
                'message' => __('app_translation.destination_type_not_found')
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        // 1. Create
        $destination = Destination::create([
            "color" => $request->color,
            "destination_type_id" => $destinationType->id,
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            DestinationTrans::create([
                "value" => $request["{$name}"],
                "destination_id" => $destination->id,
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
            'destination' => [
                "id" => $destination->id,
                "name" => $name,
                "color" => $destination->color,
                "created_at" => $destination->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function update(DestinationStoreRequest $request)
    {
        $request->validated();
        // This validation not exist in UrgencyStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        // 1. Find
        $destination = Destination::find($request->id);
        if (!$destination) {
            return response()->json([
                'message' => __('app_translation.destination_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $type = DestinationType::find($request->destination_type_id);
        if (!$type) {
            return response()->json([
                'message' => __('app_translation.destination_type_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $trans = DestinationTrans::where('destination_id', $request->id)
            ->select('id', 'language_name', 'value')->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $trans->where('language_name', $code)->first();
            $tran->value = $request["{$name}"];
            $tran->save();
        }
        $destination->color = $request->color;
        $destination->destination_type_id = $request->destination_type_id;
        $destination->save();

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else {
            $name = $request->pashto;
        }

        return response()->json([
            'message' => __('app_translation.success'),
            'destination' => [
                "id" => $destination->id,
                "color" => $destination->color,
                "name" => $name,
                "created_at" => $destination->created_at,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function destroy($id)
    {
        $destination = Destination::find($id);
        if ($destination) {
            $destination->delete();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
    }
}
