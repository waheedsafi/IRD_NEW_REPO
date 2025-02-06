<?php

namespace App\Http\Controllers\api\app\director;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\ngo\director\UpdateDirectorRequest;
use App\Models\Address;
use App\Models\AddressTran;
use App\Models\Contact;
use App\Models\Director;
use App\Models\DirectorTran;
use App\Models\Email;
use App\Traits\Address\AddressTrait;
use App\Traits\Director\DirectorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DirectorController extends Controller
{
    use DirectorTrait, AddressTrait;
    //

    public function store(Request $request, $ngoId)
    {
        // Validate the required fields for creating a Director
        $validated = $request->validate([
            'nid_no' => 'required|string|max:20',
            'gender_id' => 'required|integer',
            'director_country_id' => 'required|integer|exists:coutries,id',
            'director_email' => 'required|email|unique:emails,value',
            'director_contact' => 'required|unique:contacts,value',
            'director_district_id' => 'required|integer|exists:districts,id',
            'director_area' => 'required|string|max:255',
            'director_name_pastho' => 'required|string|max:255',
            'director_last_name_pastho' => 'required|string|max:255',
            'director_name_farsi' => 'required|string|max:255',
            'director_last_name_farsi' => 'required|string|max:255',
            'director_name_english' => 'required|string|max:255',
            'director_last_name_english' => 'required|string|max:255',
        ]);

        $email = Email::create(['value' => $validated['director_email']]);

        $contact = Contact::create(['value' => $validated['director_contact']]);

        // Create address
        $address = Address::create([
            'district_id' => $validated['director_district_id'],
            'area' => $validated['director_area'],
        ]);


        $profile =    $this->storeProfile($request, 'director-profile');
        $nid_attach = $this->storeDocument($request, 'private', 'document/director-Nid');

        // Create the Director record
        $director = Director::create([
            'ngo_id' => $ngoId,
            'nid_no' => $validated['nid_no'],
            'nid_attachment' => $nid_attach['path'],
            'profile'   =>  $profile,
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

        DirectorTran::create(
            [
                'director_id' => $director->id,
                'language_name' => 'en',
                'name' => $validated['name_english'],
                'last_name' => $validated['last_name_english']
            ]
        );
        DirectorTran::create(
            [
                'director_id' => $director->id,
                'language_name' => 'ps',
                'name' => $validated['name_pashto'],
                'last_name' => $validated['last_name_pastho']
            ]
        );
        DirectorTran::create(
            [
                'director_id' => $director->id,
                'language_name' => 'fa',
                'name' => $validated['name_farsi'],
                'last_name' => $validated['last_name_farsi']
            ]
        );


        return response()->json(['message' => 'Director created successfully', 'director' => $director], 201);
    }

    public function ngoDirector(Request $request, $ngo_id)
    {
        $locale = App::getLocale();
        // Joining necessary tables to fetch the NGO data
        $director = Director::join('contacts', 'contact_id', '=', 'contacts.id')
            ->leftJoin('emails', 'email_id', '=', 'emails.id')
            ->leftJoin('addresses', 'address_id', '=', 'addresses.id')
            ->leftjoin('nid_type_trans', 'nid_type_trans.nid_type_id', 'directors.nid_type_id')
            ->leftjoin('genders', 'genders.id', 'directors.gender_id')
            ->where('directors.ngo_id', $ngo_id)
            ->where('nid_type_trans.language_name', $locale)

            ->select(
                'directors.id',
                'emails.value as email',
                'contacts.value as contact',
                'directors.contact_id',
                'nid_type_trans.value as nid_type',
                'directors.nid_type_id',
                "genders.name_{$locale} as gender",
                'gender_id',
                'directors.country_id',
                'nid_no',
                'address_id',
                'province_id',
                'district_id',
            )
            ->first();

        // Handle NGO not found
        if (!$director) {
            return response()->json([
                'message' => __('app_translation.not_found'),
            ], 404);
        }


        // Fetching translations using a separate query
        $translations = $this->directorNameTrans($director->id);
        $areaTrans = $this->getAddressAreaTran($director->address_id);
        $address = $this->getCompleteAddress($director->address_id, $locale);


        $data = [
            'id' => $director->id,
            'name_english' => $translations['en']->name ?? '',
            'name_pashto' => $translations['ps']->name ?? '',
            'name_farsi' => $translations['fa']->name ?? '',
            'surname_english' => $translations['en']->last_name ?? '',
            'surname_pashto' => $translations['ps']->last_name ?? '',
            'surname_farsi' => $translations['fa']->last_name ?? '',
            'nationality' => ['name' => $this->getCountry($director->country_id, $locale), 'id' => $director->country_id],
            'contact' => $director->contact,
            'email' => $director->email,
            'gender' => ['name' => $director->gender, 'id' => $director->gender_id],
            'nid' => $director->nid_no,
            'identity_type' => ['name' => $director->nid_type, 'id' => $director->nid_type_id],
            'province' => ['name' => $address['province'], 'id' => $director->province_id],
            'district' => ['name' => $address['district'], 'id' => $director->district_id],
            'area_english' => $areaTrans['en']->area ?? '',
            'area_pashto' => $areaTrans['ps']->area ?? '',
            'area_farsi' => $areaTrans['fa']->area ?? '',
        ];

        return response()->json([
            'message' => __('app_translation.success'),
            'director' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function ngoDirectors($ngo_id)
    {
        $locale = App::getLocale();
        $directors = DB::table('directors as d')
            ->where('d.ngo_id', $ngo_id)
            ->join('director_trans as dt', function ($join) use ($locale) {
                $join->on('dt.director_id', '=', 'd.id')
                    ->where('dt.language_name', '=', $locale);
            })
            ->join('contacts as c', 'd.contact_id', '=', 'c.id')
            ->join('emails as e', 'd.email_id', '=', 'e.id')
            ->select(
                'd.id',
                'd.is_active',
                'dt.name',
                'dt.last_name as surname',
                'c.value as contact',
                'e.value as email',
            )
            ->get();

        return response()->json([
            'message' => __('app_translation.success'),
            'directors' => $directors,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(UpdateDirectorRequest $request, $id)
    {

        $validateData = $request->validate();
        $director =     Director::find($id);
        $address = Address::find($director->address_id);
        $address_en_tran = AddressTran::where('address_id', $director->address_id)->where('langauge_name', 'en');
        $address_fa_tran = AddressTran::where('address_id', $director->address_id)->where('langauge_name', 'fa');
        $address_ps_tran = AddressTran::where('address_id', $director->address_id)->where('langauge_name', 'ps');
        $director_en_tran = DirectorTran::where('director_id', $id)->where('language_name', 'en')->first();
        $director_ps_tran = DirectorTran::where('director_id', $id)->where('language_name', 'ps')->first();
        $director_fa_tran = DirectorTran::where('director_id', $id)->where('language_name', 'fa')->first();
        $director->gender_id = $validateData['gender']['id'];
        $director->country_id = $validateData['country']['id'];
        $director->nid_type_id = $validateData['nid_type']['nid_id'];
        $director->nid_no = $validateData['nid_no'];
        $address->province_id = $validateData['province']['id'];
        $address->district_id = $validateData['district']['id'];
        $address_en_tran->area = $validateData['area_english'];
        $address_ps_tran->area = $validateData['area_pashto'];
        $address_fa_tran->area = $validateData['area_farsi'];
        $director_en_tran->name = $validateData['name_english'];
        $director_ps_tran->name = $validateData['name_pashto'];
        $director_fa_tran->name = $validateData['name_farsi'];
        $director_en_tran->last_name = $validateData['last_name_english'];
        $director_ps_tran->last_name = $validateData['last_name_pashto'];
        $director_fa_tran->last_name = $validateData['last_name_farsi'];


        DB::beginTransaction();
        Email::where('id', $director->email_id)->update(['value' => $validateData['email']]);
        Contact::where('id', $director->contact_id)->update(['value' => $validateData['contact']]);

        $director->save();
        $director_en_tran->save();
        $director_fa_tran->save();
        $director_ps_tran->save();
        $address->save();
        $address_en_tran->save();
        $address_fa_tran->save();
        $address_ps_tran->save();


        return response()->json([
            'message' => __('app_translation.success'),
            'director' => $request,
        ], 200, [], JSON_UNESCAPED_UNICODE);


        DB::commit();
    }
}
