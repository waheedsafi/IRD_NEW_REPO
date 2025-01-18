<?php

namespace App\Http\Controllers\api\app\donor;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\donor\DonorRegisterRequest;
use App\Models\Contact;
use App\Models\Donor;
use App\Models\DonorTran;
use App\Models\Email;
use Illuminate\Support\Facades\Hash;

class DonorController extends Controller
{
    //
    public function store(DonorRegisterRequest $request)
    {

        $validatedData = $request->validated();

        // Create email
        $email = Email::create(['value' => $validatedData['email']]);

        $contact = Contact::create(['value' => $validatedData['contact']]);


        $path = '';
        if ($request->profile) {
            $path = $this->storeProfile($request);
        }
        // Create NGO
        $newDonor = Donor::create([
            'username' => $validatedData['username'],
            'email_id' => $email->id,
            'contact' => $contact->id,
            'profile' => $path,
            "password" => Hash::make($validatedData['password']),
        ]);



        DonorTran::create([
            'ngo_id' => $newDonor->id,
            'language_name' =>  LanguageEnum::default->value,
            'name' => $validatedData['name_en'],

        ]);




        return response()->json(['message' => __('app_translation.success')], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
