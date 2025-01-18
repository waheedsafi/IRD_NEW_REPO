<?php

namespace App\Http\Controllers\api\app\ngo;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\NgoType;
use Illuminate\Support\Facades\App;

class NgoTypeController extends Controller
{
    public function types()
    {
        $locale = App::getLocale();
        $tr = NgoType::join('ngo_type_trans', 'ngo_types.id', '=', 'ngo_type_trans.ngo_type_id')
            ->where('ngo_type_trans.language_name', $locale)
            ->select('ngo_type_trans.value as name', 'ngo_types.id')
            ->orderBy('ngo_types.id', 'desc')
            ->get();

        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
