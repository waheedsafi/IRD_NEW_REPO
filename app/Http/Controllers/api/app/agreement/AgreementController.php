<?php

namespace App\Http\Controllers\api\app\agreement;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    //

    public function agreement(Request $request, $id)
    {

        $data =    Agreement::select('id', 'start_date', 'end_date')->where('ngo_id', $id)->get();


        return response()->json([
            'message' => __('app_translation.success'),
            'director' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
