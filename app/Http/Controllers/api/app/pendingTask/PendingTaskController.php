<?php

namespace App\Http\Controllers\api\app\pendingTask;

use App\Http\Controllers\Controller;
use App\Models\PendingTask;
use Illuminate\Http\Request;

class PendingTaskController extends Controller
{
    //

    public function storeContent(Request $request){
    
        
     

        if($request->pending_id){

               $request->validate([
            'contents' =>'required|string'
        ]);

         $pendingTask =   PendingTask::find($request->pending_id);

         $pendingTask->content = $request->contents;
         $pendingTask->save();

                    return response()->json(
            [
                'message' => __('app_translation.success'),

            ],
           
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
        }
        else{
            
            $request->validate([
            'contents' =>'required|string',
            'task_type' =>'required',

        ]);



            return PendingTask::create([
            "task_type" => $request->task_type,
            "content" => $request->contents,
            "user_id" => $request->user()->id,
            "user_type" => $request->user()->role_id,
        ]);


          return response()->json(
            [
                'message' => __('app_translation.success'),

            ],
           
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
        }







    }


    public function content(Request $request){

        

    }
}
