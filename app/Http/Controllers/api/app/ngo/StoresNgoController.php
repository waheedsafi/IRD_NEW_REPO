<?php

namespace App\Http\Controllers\api\app\ngo;

use Carbon\Carbon;
use App\Models\Ngo;
use App\Models\Email;
use App\Enums\RoleEnum;
use App\Models\Address;
use App\Models\Contact;
use App\Models\NgoTran;
use App\Models\NgoStatus;
use App\Enums\LanguageEnum;
use App\Enums\PermissionEnum;
use App\Models\AddressTran;
use App\Models\PendingTask;
use Illuminate\Http\Request;
use App\Models\StatusTypeTran;
use App\Enums\Type\TaskTypeEnum;
use App\Enums\Type\StatusTypeEnum;
use App\Models\PendingTaskContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\app\ngo\NgoRegisterRequest;
use App\Http\Requests\app\ngo\NgoInitStoreRequest;
use App\Models\NgoPermission;

class StoresNgoController extends Controller
{
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

        $status = StatusTypeTran::where('status_type_id', StatusTypeEnum::not_logged_in->value)
            ->select('name')->first();
        return response()->json(
            [
                'message' => __('app_translation.success'),
                "ngo" => [
                    "id" => $newNgo->id,
                    "profile" => $newNgo->profile,
                    "abbr" => $newNgo->abbr,
                    "registration_no" => $newNgo->registration_no,
                    "status_id" => StatusTypeEnum::not_logged_in->value,
                    "status" => $status->name,
                    "type_id" => $validatedData['ngo_type_id'],
                    "establishment_date" => null,
                    "name" => $name,
                    "contact" => $validatedData['contact'],
                    "email" => $validatedData['email'],
                    "created_at" => $newNgo->created_at,
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function storePersonalDetial(Request $request, $id)
    {
        $request->validate([
            'contents' => 'required|string',
            'step' => 'required|string',
        ]);

        $user = $request->user();
        $user_id = $user->id;
        $role = $user->role_id;
        $task_type = TaskTypeEnum::ngo_registeration;

        $task = PendingTask::where('user_id', $user_id)
            ->where('user_type', $role)
            ->where('task_type', $task_type)
            ->where('task_id', $id)
            ->first(); // Retrieve the first matching record

        if ($task) {
            $pendingContent = PendingTaskContent::where('pending_task_id', $task->id)
                ->first(); // Get the maximum step value
            if ($pendingContent) {
                // Update prevouis content
                $pendingContent->step = $request->step;
                $pendingContent->content = $request->contents;
                $pendingContent->save();
            } else {
                // If no content found
                PendingTaskContent::create([
                    'step' => $request->step,
                    'content' => $request->contents,
                    'pending_task_id' => $task->id
                ]);
            }
        } else {
            $task =  PendingTask::create([
                'user_id' => $user_id,
                'user_type' => $role,
                'task_type' => $task_type,
                'task_id' => $id
            ]);
            PendingTaskContent::create([
                'step' => 1,
                'content' => $request->contents,
                'pending_task_id' => $task->id
            ]);
        }
        return response()->json(
            [
                'message' => __('app_translation.success'),
            ],

            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function storePersonalDetialFinal(NgoInitStoreRequest $request)
    {
        $request->validated();
        Log::error($request);
        return $request;
    }

    public function ngoPermissions($ngo_id)
    {
        NgoPermission::create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "ngo_id" => $ngo_id,
            "permission" => PermissionEnum::ngo->value,
        ]);
        NgoPermission::create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "ngo_id" => $ngo_id,
            "permission" => PermissionEnum::projects->value,
        ]);
    }
}
