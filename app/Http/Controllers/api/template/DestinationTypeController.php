<?php

namespace App\Http\Controllers\api\template;

use Exception;
use App\Models\Translate;
use App\Enums\LanguageEnum;
use App\Models\DestinationType;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\template\destination\DestinationTypeStoreRequest;
use Illuminate\Support\Facades\DB;

class DestinationTypeController extends Controller
{
    public function DestinationTypes()
    {
        $locale = App::getLocale();
        $tr = DB::table("destination_types as dt")
            ->join('destination_type_trans as dtt', function ($join) use ($locale) {
                return $join->on('dtt.destination_type_id', 'dt.id')
                    ->where('language_name', $locale);
            })->select('dt.id', 'dtt.value as name')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(DestinationTypeStoreRequest $request)
    {
        $payload = $request->validated();
        try {
            // 1. Create
            $destinationType = DestinationType::create([
                "name" => $payload["english"]
            ]);
            if ($destinationType) {
                // 1. Translate
                $this->TranslateFarsi($payload["farsi"], $destinationType->id, DestinationType::class);
                $this->TranslatePashto($payload["pashto"], $destinationType->id, DestinationType::class);
                // Get local
                $locale = App::getLocale();
                if ($locale === LanguageEnum::default->value) {
                    return response()->json([
                        'message' => __('app_translation.success'),
                        'destinationType' => [
                            "id" => $destinationType->id,
                            "name" => $destinationType->name,
                            "createdAt" => $destinationType->created_at
                        ]
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                } else if ($locale === LanguageEnum::pashto->value) {
                    return response()->json([
                        'message' => __('app_translation.success'),
                        'destinationType' => [
                            "id" => $destinationType->id,
                            "name" => $payload["pashto"],
                            "createdAt" => $destinationType->created_at
                        ]
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                } else {
                    return response()->json([
                        'message' => __('app_translation.success'),
                        'destinationType' => [
                            "id" => $destinationType->id,
                            "name" => $payload["farsi"],
                            "createdAt" => $destinationType->created_at
                        ]
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                }

                return response()->json([
                    'message' => __('app_translation.success'),
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else
                return response()->json([
                    'message' => __('app_translation.failed'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            return response()->json([
                'message' => __('app_translation.server_error')
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function destroy($id)
    {
        try {
            $destinationType = DestinationType::find($id);
            if ($destinationType) {
                // 1. Delete Translation
                Translate::where("translable_id", "=", $id)
                    ->where('translable_type', '=', DestinationType::class)->delete();
                $destinationType->delete();
                return response()->json([
                    'message' => __('app_translation.success'),
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else
                return response()->json([
                    'message' => __('app_translation.failed'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            return response()->json([
                'message' => __('app_translation.server_error')
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function destinationType($id)
    {
        try {
            $destinationType = DestinationType::find($id);
            if ($destinationType) {
                $data = [
                    "id" => $destinationType->id,
                    "en" => $destinationType->name,
                ];
                $translations = Translate::where("translable_id", "=", $id)
                    ->where('translable_type', '=', DestinationType::class)->get();
                foreach ($translations as $translation) {
                    $data[$translation->language_name] = $translation->value;
                }
                return response()->json([
                    'destinationType' =>  $data,
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else
                return response()->json([
                    'message' => __('app_translation.failed'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            return response()->json([
                'message' => __('app_translation.server_error')
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function update(DestinationTypeStoreRequest $request)
    {
        $payload = $request->validated();
        // This validation not exist in DestinationTypeStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        try {
            // 1. Find
            $destinationType = DestinationType::find($request->id);
            if ($destinationType) {
                $locale = App::getLocale();
                // 1. Update
                $destinationType->name = $payload['english'];
                $destinationType->save();
                $translations = Translate::where("translable_id", "=", $destinationType->id)
                    ->where('translable_type', '=', DestinationType::class)->get();
                foreach ($translations as $translation) {
                    if ($translation->language_name === LanguageEnum::farsi->value) {
                        $translation->value = $payload['farsi'];
                    } else if ($translation->language_name === LanguageEnum::pashto->value) {
                        $translation->value = $payload['pashto'];
                    }
                    $translation->save();
                }
                if ($locale === LanguageEnum::pashto->value) {
                    $destinationType->name = $payload['pashto'];
                } else if ($locale === LanguageEnum::farsi->value) {
                    $destinationType->name = $payload['farsi'];
                }
                return response()->json([
                    'message' => __('app_translation.success'),
                    'destinationType' => [
                        "id" => $destinationType->id,
                        "name" => $payload["farsi"],
                        "createdAt" => $destinationType->created_at
                    ],
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else
                return response()->json([
                    'message' => __('app_translation.failed'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            return response()->json([
                'message' => __('app_translation.server_error')
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
