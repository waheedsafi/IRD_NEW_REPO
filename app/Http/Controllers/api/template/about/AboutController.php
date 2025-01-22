<?php

namespace App\Http\Controllers\api\template\about;

use App\Http\Controllers\Controller;
use App\Http\Requests\template\about\AboutStoreRequest;
use App\Http\Requests\template\about\AboutUpdateRequest;
use App\Models\OfficeInformation;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    //


    public function abouts(){

    $data=OfficeInformation::all();


             return response()->json([
            "about" => $data

        ], 200, [], JSON_UNESCAPED_UNICODE);
        
    }

    public function about(Request $request,$id){

        $data =OfficeInformation::find($id);

          return response()->json([
            "about" => [
                'address_english' => $data->address_en,
                'address_pashto' => $data->address_ps,
                'address_farsi' => $data->address_fa,
                'contact' =>$data->contact,
                'email' =>$data->email
            ]

        ], 200, [], JSON_UNESCAPED_UNICODE);


    }

    public function store(AboutStoreRequest $request){
   

       $validatedData = $request->validate();

     $about =  OfficeInformation::create([
        'address_en' =>$request->address_english,
        'address_ps' =>$request->address_pashto,
        'address_fa' =>$request->address_farsi,
        'contact' =>$request->contact,
        'email' =>$request->email,

       ]);


       
        return response()->json(
            [
                'message' => __('app_translation.success'),
                'about' => [
                    "id" => $about->id,
                    "address_english" => $about->address_en,
                    "address_pashto" => $about->address_ps,
                    "address_farsi" => $about->address_fa,
                    "contact" => $about->contact,
                    "email" => $about->email,
                    
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );

    }

    public function update(AboutUpdateRequest $request){

        
       $validatedData = $request->validate();

       $data =OfficeInformation::finc($validatedData['id']);


       $data->address_en = $validatedData['address_english'];
       $data->address_ps =$validatedData['address_pashto'];
       $data->address_fa =$validatedData['address_farsi'];
       $data->contact =$validatedData['contact'];
       $data->email =$validatedData['eamil'];
       $data->save();
    

       
        return response()->json(
            [
                'message' => __('app_translation.success'),
                'about' => [
                    "id" => $data->id,
                    "address_english" => $data->address_en,
                    "address_pashto" => $data->address_ps,
                    "address_farsi" => $data->address_fa,
                    "contact" => $data->contact,
                    "email" => $data->email,
                    
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );

    }

}


