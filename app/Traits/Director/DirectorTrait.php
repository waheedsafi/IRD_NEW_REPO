<?php

namespace App\Traits\Director;

use App\Models\Email;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Director;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use App\Models\DirectorTran;

trait DirectorTrait
{
  public function directorNameTrans($director_id)
  {
    $translations = DirectorTran::where('director_id', $director_id)
      ->select('language_name', 'name', 'last_name')
      ->get()
      ->keyBy('language_name');
    return $translations;
  }
  public function storeDirector($ngo_id, $request)
  {
    $email = Email::where('value', '=', $request->director_email)->first();
    if ($email) {
      return [
        "response" =>
        response()->json([
          'message' => __('app_translation.email_exist'),
        ], 400, [], JSON_UNESCAPED_UNICODE),
        "success" => false
      ];
    }
    $contact = Contact::where('value', '=', $request->director_contact)->first();
    if ($contact) {
      return [
        "response" =>
        response()->json([
          'message' => __('app_translation.contact_exist'),
        ], 400, [], JSON_UNESCAPED_UNICODE),
        "success" => false
      ];
    }

    // 3. Create address
    $address = Address::create([
      'province_id' => $request->director_province->id,
      'district_id' => $request->director_dis->id,
    ]);

    // 4. make other directors false
    Director::where('is_active', true)
      ->where('ngo_id', $ngo_id)
      ->update(['is_active' => false]);
    // 5. Create the Director
    $director = Director::create([
      'ngo_id' => $ngo_id,
      'nid_no' => $request->nid,
      'nid_type_id' => $request->identity_type->id,
      'is_active' => 1,
      'gender_id' => $request->gender->id,
      'country_id' => $request->nationality->id,
      'address_id' => $address->id,
      'email_id' => $email->id,
      'contact_id' => $contact->id,
    ]);
    foreach (LanguageEnum::LANGUAGES as $code => $name) {
      DirectorTran::create([
        'director_id' => $director->id,
        'language_name' => $code,
        'name' => $request["director_name_{$name}"],
        'last_name' => $request["surname_{$name}"],
      ]);

      AddressTran::create([
        'address_id' => $address->id,
        'language_name' => $code,
        'area' => $request["director_area_{$name}"],
      ]);
    }
    return [
      "success" => true,
      "director" => $director
    ];
  }
  public function updateDirector($ngo_id, $request) {}
}
