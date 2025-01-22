<?php

namespace App\Http\Controllers\api\template\Staff;

use App\Enums\StaffEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\template\staff\StaffStoreRequest;
use App\Http\Requests\template\staff\StaffUpdateRequest;
use App\Models\Staff;
use App\Models\StaffTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class StaffController extends Controller
{
    //


public function manager(){

    $locale =App::getLocale();
   $data = Staff::with([
         'staffTran' => function ($query) use ($locale) {
        $query->select('staff_id', 'name')->where('language_name',$locale);
        },
        'staffType:id,name'
    ])->where('staff_type_id',StaffEnum::manager)->get();



    return response()->json([
            "manager" => $data,

        ], 200, [], JSON_UNESCAPED_UNICODE);

}
public function director(){

            $locale =App::getLocale();
        $data = Staff::with([
                'staffTran' => function ($query) use ($locale) {
                $query->select('staff_id', 'name')->where('language_name',$locale);
                },
                'staffType:id,name'
            ])->where('staff_type_id',StaffEnum::director)->get();



            return response()->json([
                    "director" => $data,

                ], 200, [], JSON_UNESCAPED_UNICODE);

}
public function technicalSupports(){

        $locale =App::getLocale();
        $data = Staff::with([
                'staffTran' => function ($query) use ($locale) {
                $query->select('staff_id', 'name')->where('language_name',$locale);
                },
                'staffType:id,name'
            ])->where('staff_type_id',StaffEnum::technical_support)->get();



            return response()->json([
                    "director" => $data,

                ], 200, [], JSON_UNESCAPED_UNICODE);

}


  public function staff(Request $request,$id){

  
      $locale = App::getLocale();
  
    $staff = Staff::with([
        'staffType:id,name'
    ])->find($id);


  

        // Fetch translations for the staff
        $translations = StaffTran::where('staff_id', $id)
            ->whereIn('language_name', ['en', 'ps', 'fa'])
            ->get()
            ->keyBy('language_name');

        // Retrieve individual translations or set defaults
        $staffEnTran = $translations->get('en', (object) ['name' => '']);
        $staffPsTran = $translations->get('ps', (object) ['name' => '']);
        $staffFaTran = $translations->get('fa', (object) ['name' => '']);

        // Prepare the response
        return response()->json([
            'staff' => [
                'id' => $staff->id,
                'name_english' => $staffEnTran->name,
                'name_pashto' => $staffPsTran->name,
                'name_farsi' => $staffFaTran->name,
                'staff_type' => [
                    'id' => $staff->staff_type_id,
                    'value' => $staff->staffType->name
                     
                ],
             
             
                'contact' => $staff->contact,
                'email' => $staff->email,
                'profile' =>$staff->profile
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
 }


 public function store(StaffStoreRequest $request)
{
    $locale = App::getLocale();
    
    // Validate the request
    $validateData = $request->validated(); // Use validated() for already validated data

    // Store the profile
    $profile = $this->storeProfile($request, 'staff-profile');
    
    // Store Staff data
    $staff = Staff::create([
        'contact' => $validateData['contact'],
        'email' => $validateData['email'],
        'staff_type_id' => $validateData['staff_type_id'],
        'profile' => $profile,
    ]);
    
    // Handle translation insertion
    $languages = ['en', 'ps', 'fa'];
    foreach ($languages as $language) {
        StaffTran::create([
            'language_name' => $language,
            'staff_id' => $staff->id,
            'name' => $validateData["name_{$language}"],
        ]);
    }

    // Set name based on locale
    $name = $validateData["name_{$locale}"] ?? $validateData['name_english'];  // Default to English

    return response()->json([
        'message' => __('app_translation.success'),
        'staff' => [
            "id" => $staff->id,
            "name" => $name,
            "contact" => $validateData['contact'],
            "email" => $validateData['email'],
            "profile" => $profile,
        ]
    ], 200, [], JSON_UNESCAPED_UNICODE);
}

public function update(StaffUpdateRequest $request)
{
    $locale = App::getLocale();
    
    // Validate the request
    $validateData = $request->validated();
    

    // Find the staff entry by ID
    $staff = Staff::findOrFail( $validateData['id']);

    // Update Staff details
    $staff->update([
        'contact' => $validateData['contact'],
        'email' => $validateData['email'],
        'staff_type_id' => $validateData['staff_type_id'],
    ]);

    // Update the profile if provided
    if ($request->hasFile('profile')) {
        $profile = $this->storeProfile($request, 'staff-profile');
        $staff->update(['profile' => $profile]);
    }

    // Update or create translations
    $languages = ['en', 'ps', 'fa'];
    foreach ($languages as $language) {
        StaffTran::updateOrCreate(
            [
                'staff_id' => $staff->id,
                'language_name' => $language,
            ],
            [
                'name' => $validateData["name_{$language}"],
            ]
        );
    }

    // Set name based on locale
    $name = $validateData["name_{$locale}"] ?? $validateData['name_english'];

    return response()->json([
        'message' => __('app_translation.success'),
        'staff' => [
            "id" => $staff->id,
            "name" => $name,
            "contact" => $validateData['contact'],
            "email" => $validateData['email'],
            "profile" => $staff->profile,
        ]
    ], 200, [], JSON_UNESCAPED_UNICODE);
}








}

