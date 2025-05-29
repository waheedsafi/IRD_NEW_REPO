<?php

namespace App\Http\Controllers\api\app\donor;

use App\Models\Donor;
use App\Models\Email;
use App\Models\Contact;
use App\Models\DonorTran;
use App\Enums\LanguageEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\app\donor\DonorRegisterRequest;
use App\Traits\Helper\FilterTrait;

class DonorController extends Controller
{


    use FilterTrait;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();

        $query = DB::table('donors as don')
            ->join('donor_trans as dont', function ($join) use ($locale) {
                $join->on('don.id', '=', 'dont.donor_id')
                    ->where('dont.language_name', $locale);
            })
            ->join('emails as e', 'e.id', '=', 'n.email_id')
            ->join('contacts as c', 'c.id', '=', 'n.contact_id')
            ->select(
                'don.id',
                'don.profile',
                'don.username',
                'dont.name as name',
                'e.value as email',
                'c.value as contact',
                'don.created_at'
            );

        $this->applyDate($query, $request, 'don.created_at', 'don.created_at');
        $allowColumn = [
            'name' => 'dont.name',
            'username' => 'don.username'
        ];
        $this->applyFilters($query, $request, $allowColumn);

        $this->applySearch($query, $request, $allowColumn);

        $result = $query->paginate($perPage, ['*'], 'page', $page);


        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'donor' => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
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
