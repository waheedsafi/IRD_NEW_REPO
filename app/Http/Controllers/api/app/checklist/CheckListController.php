<?php

namespace App\Http\Controllers\api\app\checklist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class CheckListController extends Controller
{
    public function internalCheckList()
    {
        $locale = App::getLocale();

        $tr =  [];

        return response()->json([
            'ngos' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function externalCheckList()
    {
        $locale = App::getLocale();
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
}
