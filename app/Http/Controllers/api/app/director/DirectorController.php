<?php

namespace App\Http\Controllers\api\app\director;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Director;
use App\Models\DirectorTran;
use App\Models\Email;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    //

    public function store(Request $request, $ngoId)
{
    // Validate the required fields for creating a Director
    $validated = $request->validate([
        'nid_no' => 'required|string|max:20',
        'gender_id' => 'required|integer',       
        'director_country_id' => 'required|integer|exists:coutries,id',    
        'director_email' => 'required|email|unique:emails,value',
        'director_contact' => 'required|regex:/^[0-9]{10}$/|unique:contacts,value',
        'director_district_id' => 'required|integer|exists:districts,id',
        'director_area' => 'required|string|max:255',
        'director_name_ps' => 'required|string|max:255',
        'director_last_name_ps' => 'required|string|max:255',
        'director_name_fa' => 'required|string|max:255',
        'director_last_name_fa' => 'required|string|max:255',
        'director_name_en' => 'required|string|max:255',
        'director_last_name_en' => 'required|string|max:255',
    ]);

      $email = Email::create(['value' => $validated['director_email']]);

        $contact = Contact::create(['value' => $validated['director_contact']]);

        // Create address
        $address = Address::create([
            'district_id' => $validated['director_district_id'],
            'area' => $validated['director_area'],
        ]);

    // Create the Director record
    $director = Director::create([
        'ngo_id' => $ngoId,
        'nid_no' => $validated['nid_no'],
        'nid_attachment' => $validated['name_en'],   
        'nid_type_id' => $validated['nid_type_id'],      
        'is_Active' => 1,                            
        'gender_id' => $validated['gender_id'],      
        'country_id' => $validated['director_country_id'],
        'address_id' => $address->id,
        'email_id' => $email->id,
        'contact_id' => $contact->id,
    ]);

    // Define available languages and create/update translations
    $languages = ['ps', 'fa', 'en'];

    foreach ($languages as $suffix) {
        DirectorTran::updateOrCreate(
            ['director_id' => $director->id, 'language_name' => $suffix],
            [
                'name' => $validated["director_name_{$suffix}"], // Example: director_name_ps
                'last_name' => $validated["director_last_name_{$suffix}"], // Example: director_last_name_ps
            ]
        );
    }

    return response()->json(['message' => 'Director created successfully', 'director' => $director], 201);
}

}
