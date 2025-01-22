<?php

namespace App\Http\Controllers\api\app\ngo;

use App\Models\Ngo;
use App\Models\Email;
use App\Models\Address;
use App\Models\NgoTran;
use App\Enums\LanguageEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusTypeEnum;
use App\Http\Requests\app\ngo\NgoProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\app\ngo\NgoRegisterRequest;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AddressTran;
use App\Models\NgoStatus;

class NgoController extends Controller
{


    public function ngos(Request $request, $page)
    {
        $locale = App::getLocale();
            $perPage = $request->input('per_page', 10); // Number of records per page
            $page = $request->input('page', 1); // Current page

        // Eager loading relationships
        $query = Ngo::with([
            'ngoTrans' => function ($query) use ($locale) {
                $query->where('language_name', $locale)->select('id', 'ngo_id', 'name');
            },
            'ngoType' => function ($query) use ($locale) {
                $query->with(['ngoTypeTrans' => function ($query) use ($locale) {
                    $query->where('language_name', $locale)->select('ngo_type_id', 'value as name');
                }]);
            },
            'ngoStatus' => function ($query) {
                $query->select('ngo_id');
            },
            'agreement' => function ($query) {
                $query->select('ngo_id', 'end_date');
            },
        ])
            ->select([
                'id',
                'registration_no',
                'date_of_establishment',
                'ngo_type_id',
            ]);


        // Apply filters
        $this->applyDateFilters($query, $request->input('filters.date.startDate'), $request->input('filters.date.endDate'));
        $this->applySearchFilter($query, $request->input('filters.search'));

        // Apply sorting
        $sort = $request->input('filters.sort', 'registration_no');
        $order = $request->input('filters.order', 'asc');
        $query->orderBy($sort, $order);

        // Paginate results
        $result = $query->paginate($perPage, ['*'], 'page', $page);

        // Return JSON response
        return response()->json(
            ["ngos" => $result],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    private function applySearchFilter($query, $search)
    {
        if (!empty($search['column']) && !empty($search['value'])) {
            $allowedColumns = ['registration_no', 'id', 'ngoType.name', 'ngoTran.name'];

            if (in_array($search['column'], $allowedColumns)) {
                if ($search['column'] == 'ngoType.name') {
                    // Search in ngoType's name (aliased as type_name)
                    $query->whereHas('ngoType', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search['value'] . '%');
                    });
                } elseif ($search['column'] == 'ngoTran.name') {
                    // Search in ngoTran's name (aliased as ngo_name)
                    $query->whereHas('ngoTran', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search['value'] . '%');
                    });
                } else {
                    // Default search for registration_no or id
                    $query->where($search['column'], 'like', '%' . $search['value'] . '%');
                }
            }
        }
    }

    private function applyDateFilters($query, $startDate, $endDate)
    {
        if ($startDate || $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('ngos.date_of_establishment', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->where('ngos.date_of_establishment', '>=', $startDate);
            } elseif ($endDate) {
                $query->where('ngos.date_of_establishment', '<=', $endDate);
            }
        }
    }

    public function store(NgoRegisterRequest $request)
    {
        $validatedData = $request->validated();
        $locale = App::getLocale();
        // Begin transaction
        DB::beginTransaction();
        // Create email
        $email = Email::create(['value' => $validatedData['email']]);
        $contact = Contact::create(['value' => $validatedData['contact']]);
        // Create address
        $address = Address::create([
            'district_id' => $validatedData['district_id'],
            'province_id' => $validatedData['province_id'],
        ]);
        AddressTran::create([
            'address_id' => $address->id,
            'area' => $validatedData['area_english'],
            'language_name' =>  LanguageEnum::default->value,
        ]);
        AddressTran::create([
            'address_id' => $address->id,
            'area' => $validatedData['area_pashto'],
            'language_name' =>  LanguageEnum::pashto->value,
        ]);
        AddressTran::create([
            'address_id' => $address->id,
            'area' => $validatedData['area_farsi'],
            'language_name' =>  LanguageEnum::farsi->value,
        ]);
        // Create NGO
        $newNgo = Ngo::create([
            'abbr' => $validatedData['abbr'],
            'registration_no' => "",
            'role_id' => RoleEnum::ngo->value,
            'ngo_type_id' => $validatedData['ngo_type_id'],
            'address_id' => $address->id,
            'email_id' => $email->id,
            'username' => $request->username,
            'contact_id' => $contact->id,
            "password" => Hash::make($validatedData['password']),
        ]);

        // Crea a registration_no
        $newNgo->registration_no = "IRD" . '-' . Carbon::now()->year . '-' . $newNgo->id;
        $newNgo->save();
        // Set ngo status
        NgoStatus::create([
            "ngo_id" => $newNgo->id,
            "status_type_id" => StatusTypeEnum::not_logged_in->value,
            "comment" => "Newly Created"
        ]);
        NgoTran::create([
            'ngo_id' => $newNgo->id,
            'language_name' =>  LanguageEnum::default->value,
            'name' => $validatedData['name_english'],
        ]);

        NgoTran::create([
            'ngo_id' => $newNgo->id,
            'language_name' =>  LanguageEnum::farsi->value,
            'name' => $validatedData['name_farsi'],
        ]);
        NgoTran::create([
            'ngo_id' => $newNgo->id,
            'language_name' =>  LanguageEnum::pashto->value,
            'name' => $validatedData['name_pashto'],
        ]);

        $name =  $validatedData['name_english'];
        if ($locale == LanguageEnum::farsi->value) {
            $name = $validatedData['name_farsi'];
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $validatedData['name_pashto'];
        }
        // If everything goes well, commit the transaction
        DB::commit();
        return response()->json(
            [
                'message' => __('app_translation.success'),
                "ngo" => [
                    "id" => $newNgo->id,
                    "profile" => $newNgo->profile,
                    "registrationNo" => $newNgo->registration_no,
                    "name" => $name,
                    "contact" => $contact,
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function profileUpdate(NgoProfileUpdateRequest $request, $id)
    {


        // Find the NGO
        $ngo = Ngo::find($id);

        if (!$ngo || $ngo->is_editable != 1) {
            return response()->json(['message' => __('app_translation.notEditable')], 403);
        }


        $validatedData = $request->validated();

        try {
            // Begin transaction
            DB::beginTransaction();

            $path = $this->storeProfile($request, 'ngo-profile');
            $ngo->update([
                "profile" =>  $path,
            ]);
            // Update default language record
            $ngoTran = NgoTran::where('ngo_id', $id)
                ->where('language_name', LanguageEnum::default->value)
                ->first();

            if ($ngoTran) {
                $ngoTran->update([
                    'name' => $validatedData['name_en'],
                    'vision' => $validatedData['vision_en'],
                    'mission' => $validatedData['mission_en'],
                    'general_objective' => $validatedData['general_objective_en'],
                    'objective' => $validatedData['objective_en'],
                    'introduction' => $validatedData['introduction_en']
                ]);
            } else {
                return response()->json(['message' => __('app_translation.not_found')], 404);
            }

            // Manage multilingual NgoTran records
            $languages = [
                'ps',
                'fa'

            ];

            foreach ($languages as   $suffix) {
                NgoTran::updateOrCreate(
                    ['ngo_id' => $id, 'language_name' => $suffix],
                    [
                        'name' => $validatedData["name_{$suffix}"],
                        'vision' => $validatedData["vision_{$suffix}"],
                        'mission' => $validatedData["mission_{$suffix}"],
                        'general_objective' => $validatedData["general_objective_{$suffix}"],
                        'objective' => $validatedData["objective_{$suffix}"],
                        'introduction' => $validatedData["introduction_{$suffix}"]
                    ]
                );
            }

            // Instantiate DirectorController and call its store method
            $directorController = new \App\Http\Controllers\api\app\director\DirectorController();
            $directorController->store($request, $id);

            // store document
            // Commit transaction
            DB::commit();
            return response()->json(['message' => __('app_translation.success')], 200);
        } catch (\Exception $e) {
            // Rollback on error
            DB::rollBack();
            return response()->json(['message' => __('app_translation.server_error') . $e->getMessage()], 500);
        }
    }
    public function ngoCount()
    {
        $statistics = DB::select("
        SELECT
         COUNT(*) AS count,
            (SELECT COUNT(*) FROM ngos WHERE DATE(created_at) = CURDATE()) AS todayCount,
            (SELECT COUNT(*) FROM ngos n JOIN ngo_statuses ns ON n.id = ns.ngo_id WHERE ns.status_type_id = ?) AS activeCount,
         (SELECT COUNT(*) FROM ngos n JOIN ngo_statuses ns ON n.id = ns.ngo_id WHERE ns.status_type_id = ?) AS unRegisteredCount
        FROM ngos
    ", [StatusTypeEnum::active->value, StatusTypeEnum::unregistered->value]);
        return response()->json([
            'counts' => [
                "count" => $statistics[0]->count,
                "todayCount" => $statistics[0]->todayCount,
                "activeCount" => $statistics[0]->activeCount,
                "unRegisteredCount" =>  $statistics[0]->unRegisteredCount
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
