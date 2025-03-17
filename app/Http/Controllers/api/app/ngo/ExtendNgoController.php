<?php

namespace App\Http\Controllers\api\app\ngo;

use App\Models\Ngo;
use App\Models\Email;
use App\Models\Address;
use App\Models\Contact;
use App\Models\NgoTran;
use App\Models\Director;
use App\Models\Document;
use App\Models\Agreement;
use App\Models\CheckList;
use App\Models\NgoStatus;
use App\Enums\CountryEnum;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use App\Models\Representer;
use App\Models\DirectorTran;
use App\Models\CheckListTrans;
use Illuminate\Support\Carbon;
use App\Models\RepresenterTran;
use App\Enums\CheckListTypeEnum;
use App\Enums\Type\TaskTypeEnum;
use App\Models\AgreementDirector;
use App\Models\AgreementDocument;
use App\Enums\Type\StatusTypeEnum;
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use App\Models\PendingTaskDocument;
use App\Http\Controllers\Controller;
use App\Enums\CheckList\CheckListEnum;
use App\Traits\Director\DirectorTrait;
use App\Http\Requests\app\ngo\ExtendNgoRequest;
use App\Repositories\Task\PendingTaskRepositoryInterface;
use App\Repositories\Director\DirectorRepositoryInterface;

class ExtendNgoController extends Controller
{
    //
    use HelperTrait, DirectorTrait;
    protected $pendingTaskRepository;
    protected $notificationRepository;
    protected $approvalRepository;
    protected $directorRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        DirectorRepositoryInterface $directorRepository
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;

        $this->directorRepository = $directorRepository;
    }

    public function extendNgoAgreement(ExtendNgoRequest $request)
    {
        // return $request;
        $ngo_id = $request->ngo_id;
        $request->validated();

        // Step.1
        // Email and Contact
        $ngo = Ngo::find($ngo_id);
        if (!$ngo) {
            return response()->json([
                'message' => __('app_translation.ngo_not_found'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        DB::beginTransaction();
        $email = Email::where('value', '=', $request->email)->first();
        if ($email) {
            if ($email->id != $ngo->email_id)
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
        } else {
            $email->value = $request->email;
        }
        $contact = Contact::where('value', '=', $request->contact)->first();
        if ($contact) {
            if ($contact->id != $ngo->contact_id) {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $ngo->value = $request->contact;
        }

        // Address and Ngo
        $address = Address::where('id', $ngo->address_id)
            ->select("district_id", "id", "province_id")
            ->first();
        if (!$address) {
            return response()->json([
                'message' => __('app_translation.address_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $address->province_id = $request->province->id;
        $address->district_id = $request->district->id;
        // * Update Ngo information
        $ngo->abbr = $request->abbr;
        $ngo->moe_registration_no = $request->moe_registration_no;
        $ngo->date_of_establishment = $request->establishment_date;
        $ngo->place_of_establishment = $request->country['id'];
        $ngo->ngo_type_id = $request->type['id'];
        // * Translations
        $addressTrans = AddressTran::where('address_id', $address->id)->get();
        $ngoTrans = NgoTran::where('ngo_id', $ngo->id)->get();
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $addressTran = $addressTrans->where('language_name', $code)->first();
            $ngoTran = $ngoTrans->where('language_name', $code)->first();
            $addressTran->update([
                'area' => $request["area_{$name}"],
            ]);
            $ngoTran->update([
                'name' => $request["name_{$name}"],
            ]);
        }
        $email->save();
        $contact->save();
        $ngo->save();
        $address->save();

        // Step.2 
        // Agreement
        $agreement = Agreement::create([
            'ngo_id' => $ngo->id,
            "agreement_no" => ""
        ]);
        $agreement->agreement_no = "AG" . '-' . Carbon::now()->year . '-' . $agreement->id;
        $agreement->save();
        NgoStatus::where('ngo_id', $ngo->id)->update(['is_active' => false]);
        NgoStatus::create([
            'ngo_id' => $ngo->id,
            'user_id' => $request->user()->id,
            "is_active" => true,
            'status_type_id' => StatusTypeEnum::register_form_completed,
            'comment' => 'Extend Form Complete',
        ]);
        // Step.3 
        // Director with Agreement
        $director = null;
        if ($request->new_director == true) {
            // New director is assigned
            $result = $this->storeDirector($ngo->id, $request);
            if (!$result['success']) {
                return $result['response'];
            }
            $director = $result['director'];
        } else {
            $director = $request->prev_dire;
        }
        AgreementDirector::create([
            'agreement_id' => $agreement->id,
            'director_id' => $director->id
        ]);


        // Step.4


        // 3. Ensure task exists before proceeding
        $task = $this->pendingTaskRepository->pendingTaskExist(
            $request->user(),
            TaskTypeEnum::ngo_agreement_extend,
            $id
        );
        if (!$task) {
            return response()->json([
                'error' => __('app_translation.task_not_found')
            ], 404);
        }
        // 4. Check task exists
        $exclude = [
            CheckListEnum::ngo_register_form_en->value,
            CheckListEnum::ngo_register_form_fa->value,
            CheckListEnum::ngo_register_form_ps->value,
        ];
        // If Directory Nationality is abroad ask for Work Permit
        if ($validatedData["nationality"]["id"] == CountryEnum::afghanistan->value) {
            array_push($exclude, CheckListEnum::director_work_permit->value);
        }
        // if director not select previous 
        if ($validatedData['is_previous_director'] === true) {
            array_push($exclude, CheckListEnum::director_work_permit->value);
            array_push($exclude, CheckListEnum::director_nid->value);
        }
        // if new representer the id not exists so exclude the ngo represenete letter 
        if ($validatedData['representor_id']) {
            array_push($exclude, CheckListEnum::ngo_representor_letter->value);
        }

        // 4. CheckListEnum:: task exists
        // Get checklist IDs
        $checkListIds = CheckList::where('check_list_type_id', CheckListTypeEnum::ngoRegister)
            ->whereNotIn('id', $exclude)
            ->pluck('id')
            ->toArray();
        $errors = $this->validateCheckList($task, $checkListIds);
        if ($errors) {
            return response()->json([
                'message' => __('app_translation.checklist_not_found'),
                'errors' => $errors // Reset keys for cleaner JSON output
            ], 400);
        }


        DB::beginTransaction();

        Email::where('id', $ngo->email_id)->update(['value' => $validatedData['email']]);
        Contact::where('id', $ngo->contact_id)->update(['value' => $validatedData['contact']]);


        // store ngo transalation
        $ngoTrans = NgoTran::where('ngo_id', $id)->get();
        if (!$ngoTrans) {
            return response()->json([
                'message' => __('app_translation.ngo_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $ngoTrans->where('language_name', $code)->first();
            $tran->name = $validatedData["name_{$name}"];
            $tran->vision = $validatedData["vision_{$name}"];
            $tran->mission = $validatedData["mission_{$name}"];
            $tran->general_objective = $validatedData["general_objes_{$name}"];
            $tran->objective = $validatedData["objes_in_afg_{$name}"];
            $tran->save();
        }

        // store ngo Address
        $ngo_addres = Address::find($ngo->address_id);

        $ngo_addres->province_id  = $validatedData["province"]["id"];
        $ngo_addres->district_id  = $validatedData["district"]["id"];
        $ngo_addres = AddressTran::where('address_id', $ngo->address_id)->get();

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $ngo_addres->where('language_name', $code)->first();
            $tran->area = $validatedData["area_{$name}"];
            $tran->save();
        }

        $ngo->abbr =  $validatedData["abbr"];
        $ngo->ngo_type_id  = $validatedData['type']['id'];
        $ngo->moe_registration_no  = $validatedData["moe_registration_no"];
        $ngo->place_of_establishment   = $validatedData["country"]["id"];
        $ngo->date_of_establishment  = $validatedData["establishment_date"];
        $ngo_addres->save();
        $ngo->save();



        // **Fix agreement creation**
        $agreement = Agreement::create([
            'ngo_id' => $id,
            'representer_id' => '',
            'agreement_no' => ''
        ]);
        $agreement->agreement_no = "AG" . '-' . Carbon::now()->year . '-' . $agreement->id;
        $agreement->save();

        $directorDocumentsId = [];
        $representerDocumentId = '';
        $document =  $this->documentStore($agreement->id, $id, $task->id, function ($documentData) use ($directorDocumentsId) {
            $checklist_id = $documentData['check_list_id'];
            $document = Document::create([
                'actual_name' => $documentData['actual_name'],
                'size' => $documentData['size'],
                'path' => $documentData['path'],
                'type' => $documentData['type'],
                'check_list_id' => $checklist_id,
            ]);
            if (
                $checklist_id == CheckListEnum::director_work_permit->value
                || $checklist_id == CheckListEnum::director_nid->value
            ) {
                array_push($directorDocumentsId, $document->id);
            }
            if ($checklist_id == CheckListEnum::ngo_representor_letter->value) {
                $representer_id = $document->id;
            }

            AgreementDocument::create([
                'document_id' => $document->id,
                'agreement_id' => $documentData['agreement_id'],
            ]);
        });
        if ($document) {
            return $document;
        }
        if ($validatedData['is_previous_director'] === true) {
            $director = $this->directorRepository->storeNgoDirector(
                $validatedData,
                $id,
                $agreement->id,
                $directorDocumentsId,
                true
            );
            AgreementDirector::create([
                'agreement_id' => $agreement->id,
                'director_id' => $director->id
            ]);
        }
        $representer_id = '';
        if ($validatedData['representer_id']) {
            $representer_id = $validatedData['representer_id'];
        } else {
            $representer_id =  $this->storeRepresenter($id, $request->user(), $representerDocumentId, $validatedData);
        }
        $agreement->representer_id = $representer_id;
        $agreement->save();
        $this->pendingTaskRepository->destroyPendingTask(
            $request->user(),
            TaskTypeEnum::ngo_registeration,
            $id
        );

        DB::commit();
        return response()->json(
            [
                'message' => __('app_translation.success'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    protected function validateCheckList($task, $checkListIds)
    {
        // Get checklist IDs from documents
        $documentCheckListIds = $this->pendingTaskRepository->pendingTaskDocumentQuery(
            $task->id
        )->pluck('check_list_id')
            ->toArray();

        // Find missing checklist IDs
        $missingCheckListIds = array_diff($checkListIds, $documentCheckListIds);

        if (count($missingCheckListIds) > 0) {
            // Retrieve missing checklist names
            $missingCheckListNames = CheckListTrans::whereIn('check_list_id', $missingCheckListIds)
                ->where('language_name', app()->getLocale()) // If multilingual, get current language
                ->pluck('value');


            $errors = [];
            foreach ($missingCheckListNames as $item) {
                array_push($errors, [__('app_translation.checklist_not_found') . ' ' . $item]);
            }

            return $errors;
        }

        return null;
    }
    protected function documentStore($agreement_id, $ngo_id, $pending_task_id, ?callable $callback)
    {
        // Get checklist IDs
        $documents = PendingTaskDocument::join('check_lists', 'check_lists.id', 'pending_task_documents.check_list_id')
            ->where('pending_task_id', $pending_task_id)
            ->select('size', 'path', 'check_list_id', 'actual_name', 'extension')
            ->get();

        foreach ($documents as $checklist) {
            $baseName = basename($checklist['path']);
            $oldPath = $this->getTempFullPath() . $baseName; // Absolute path of temp file

            $newDirectory = $this->ngoRegisterFolder($ngo_id, $agreement_id, $checklist['check_list_id']);

            if (!is_dir($newDirectory)) {
                mkdir($newDirectory, 0775, true);
            }
            $newPath = $newDirectory . $baseName; // Keep original filename
            $dbStorePath = $this->ngoRegisterDBPath($ngo_id, $agreement_id, $checklist['check_list_id'], $baseName);
            // Move the file
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            } else {
                return response()->json([
                    'error' => __('app_translation.file_not_found'),
                    "file" => $checklist['actual_name']
                ], 404);
            }

            $documentData = [
                'actual_name' => $checklist['actual_name'],
                'size' => $checklist['size'],
                'path' => $dbStorePath,
                'type' => $checklist['extension'],
                'check_list_id' => $checklist['check_list_id'],
                'agreement_id' => $agreement_id
            ];
            if ($callback) {
                $callback($documentData);
            }
        }
    }
    private function storeRepresenter($ngo_id, $authUser, $documentId, $validatedData)
    {
        // 1. Get current agreement
        $agreement = Agreement::where('ngo_id', $ngo_id)
            ->where('end_date', null)->where('create_at', max('create_at'))
            ->first();
        if (!$agreement) {
            return response()->json([
                'message' => __('app_translation.representor_add_error')
            ], 409);
        }
        // 2. Update prevous representors status
        Representer::where('ngo_id', $ngo_id)->update(['is_active' => false]);
        // 3. Store representor
        $representer = Representer::create([
            'ngo_id' => $ngo_id,
            'user_id' => $authUser->id,
            'is_active' => true,
            "document_id" => $documentId
        ]);
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            RepresenterTran::create([
                'representer_id' => $representer->id,
                'language_name' =>  $code,
                'full_name' => $validatedData["repre_name_{$name}"],
            ]);
        }
        return $representer->id;
    }
}
