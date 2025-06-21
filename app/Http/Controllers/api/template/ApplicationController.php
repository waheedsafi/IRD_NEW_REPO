<?php

namespace App\Http\Controllers\api\template;

use Exception;
use App\Models\Gender;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use App\Enums\LanguageEnum;
use App\Models\CurrencyTran;
use App\Models\NidTypeTrans;
use Illuminate\Http\Request;
use App\Models\NationalityTrans;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function changeLocale($locale)
    {
        try {
            // Set the language in a cookie
            if ($locale === "en" || $locale === "fa" || $locale === "ps") {
                // 1. Set app language
                App::setLocale($locale);
                return response()->json([
                    'message' => __('app_translation.lang_change_success'),
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                // 3. Passed language not exist in system
                response()->json([
                    'message' => __('app_translation.lang_change_failed'),
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            response()->json([
                'message' => __('app_translation.server_error'),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function font($direction)
    {
        try {
            $path = storage_path("app/public/fonts/");
            if ($direction === "rtl") {
                $path = $path . "nazanin.ttf";

                if (!file_exists($path)) {
                    return response()->json("Not found");
                }
                return response()->file($path);
            }
            return response()->json(["message" => "Not allowed", 405]);
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            return response()->json(['message' => "Something went wrong please try again later!"], 500);
        }
    }
    public function nationalities()
    {
        $locale = App::getLocale();
        $countryId = 2;
        $nationality = NationalityTrans::select('nationality_id as id', 'value as name')
            ->where('language_name', $locale)
            ->orderByRaw("FIELD(nationality_id," . $countryId . ") DESC") // ID 2 will come first
            ->get();

        return  response()->json(
            $nationality,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function genders()
    {
        $locale = App::getLocale();
        $gender = Gender::select('id', "name_{$locale} as name")->get();
        return response()->json($gender);
    }

    public function nidTypes()
    {
        $locale = App::getLocale();
        $nidtype =  NidTypeTrans::select('value as name', 'nid_type_id as id')
            ->where('language_name', $locale)
            ->get();
        return response()->json($nidtype);
    }

    public function currencies()
    {
        $locale = App::getLocale();
        $currency = DB::table('currencies as c')
            ->join('currency_trans as ct', function ($join) use ($locale) {
                $join->on('ct.currency_id', '=', 'c.id')
                    ->where('ct.language_name', $locale);
            })
            ->select('currency_id as id', 'name', 'symbol')
            ->get();

        return  response()->json(
            $currency,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
