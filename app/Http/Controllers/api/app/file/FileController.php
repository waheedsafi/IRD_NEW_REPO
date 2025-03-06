<?php

namespace App\Http\Controllers\api\app\file;

use Carbon\Carbon;
use App\Models\Ngo;
use App\Models\User;
use App\Models\Approval;
use App\Models\Document;
use App\Models\Agreement;
use App\Models\CheckList;
use App\Enums\NotifierEnum;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Enums\PermissionEnum;
use App\Models\AgreementDocument;
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\CheckList\CheckListEnum;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ngo\NgoRepositoryInterface;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use App\Repositories\Task\PendingTaskRepositoryInterface;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

class FileController extends Controller
{
    use HelperTrait;
    protected $pendingTaskRepository;
    protected $ngoRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        NgoRepositoryInterface $ngoRepository
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->ngoRepository = $ngoRepository;
    }
    public function checklistUploadFile(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = $request->task_type;;
            $ngo_id = $request->ngo_id;
            $checklist_id = $request->checklist_id;
            $file = $save->getFile();
            // 1. Validate checklist
            $validationResult = $this->checkListCheck($request);
            if ($validationResult !== true) {
                $filePath = $file->getRealPath();
                unlink($filePath);
                return $validationResult; // Return validation errors
            }
            // 2. Store document
            return $this->pendingTaskRepository->fileStore(
                $file,
                $request,
                $task_type,
                $checklist_id,
                $ngo_id
            );
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }

    public function checkListCheck($request)
    {
        // 1. Validate check exist
        $checklist = CheckList::find($request->checklist_id);

        if (!$checklist) {
            return response()->json([
                'message' => __('app_translation.checklist_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $rules = [
            "file" => [
                "required",
                "mimes:{$checklist->acceptable_extensions}",
                "max:{$checklist->file_size}",
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 422);
        }

        return true;
    }

    // 1. Upload files in case does not have task_id
    public function singleChecklistFileUpload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = $request->task_type;
            $check_list_id = $request->checklist_id;
            $file = $save->getFile();

            // 1. Validate checklist
            $validationResult = $this->checkListCheck($request);
            if ($validationResult !== true) {
                $filePath = $file->getRealPath();
                unlink($filePath);
                return $validationResult; // Return validation errors
            }
            // 2. Delete all previous PendingTask for current user_id, user_type and task_type
            $this->pendingTaskRepository->destroyPendingTask($request->user(), $task_type, null);
            // 3. Store new Pendding Document Task
            return $this->pendingTaskRepository->fileStore(
                $save->getFile(),
                $request,
                $task_type,
                $check_list_id,
                null
            );
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }

    public function registerSignedFormUpload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();
        if ($save->isFinished()) {
            $check_list_id = null;
            $type = $request->input('type'); // Default to 'ps' if no language is provided
            $ngo_id = $request->input('ngo_id');
            // 1. Validatation
            $ngo = Ngo::find($ngo_id);
            if (!$ngo) {
                return response()->json([
                    'message' => __('app_translation.ngo_not_found'),
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
            $agreement = Agreement::where('ngo_id', $ngo_id)
                ->where('end_date', null) // Order by end_date descending
                ->first();           // Get the first record (most recent)

            // 1. If agreement does not exists no further process.
            if (!$agreement) {
                return response()->json([
                    'message' => __('app_translation.agreement_not_exists')
                ], 409);
            }

            if ($type == "english") {
                $check_list_id = CheckListEnum::ngo_register_form_en->value;
            } else if ($type == "farsi") {
                $check_list_id = CheckListEnum::ngo_register_form_fa->value;
            } else if ($type == "pashto") {
                $check_list_id = CheckListEnum::ngo_register_form_ps->value;
            } else {
                return response()->json(
                    [
                        'message' => __('app_translation.failed')
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                );
            }
            $result = $this->ngoRepository->missingRegisterSignedForm($ngo_id);
            // Begin transaction
            DB::beginTransaction();
            if (count($result) == 0) {
                return response()->json(
                    [
                        'message' => __('app_translation.failed')
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                );
            }


            // Merge checklist_id into the request
            $request->merge(['checklist_id' => $check_list_id]);
            $file = $save->getFile();
            $validationResult = $this->checkListCheck($request);
            if (!$validationResult) {
                $filePath = $file->getRealPath();
                unlink($filePath);
                return $validationResult; // Return validation errors
            }
            // 2. Get file information
            $fileActualName = $file->getClientOriginalName();
            $fileName = $this->createChunkUploadFilename($file);
            $fileSize = $file->getSize();
            $mimetype = $file->getMimeType();
            // 3. Get director
            $directory = $this->ngoRegisterFolder($ngo_id, $agreement->id, $check_list_id);
            $dbStorePath = $this->ngoRegisterDBPath($ngo_id, $agreement->id, $check_list_id, $fileName);
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }
            $file->move($directory, $fileName);

            // 4. Store files in database
            $document = Document::create([
                'actual_name' => $fileActualName,
                'size' => $fileSize,
                'path' => $dbStorePath,
                'type' => $mimetype,
                'check_list_id' => $check_list_id,
            ]);

            AgreementDocument::create([
                'document_id' => $document->id,
                'agreement_id' => $agreement->id,
            ]);

            // 5. Check submittion is completed
            if (count($result) == 1) {
                // 6. Create a approval
                Approval::create([
                    "request_comment" => "",
                    "request_date" => Carbon::now(),
                    "requester_id" => $ngo_id,
                    "requester_type " => Ngo::class,
                    "notifier_type_id" => NotifierEnum::ngo_submitted_register_form->value,
                ]);
                // 7. Create a notification
                $authUsers = DB::table("users as u")
                    ->join("user_permissions as up", function ($join) {
                        $join->on("up.user_id", '=', 'u.id')
                            ->where('up.permission', PermissionEnum::approval->value);
                    })
                    ->select('up.user_id')
                    ->get();
                foreach ($authUsers as $user) {
                    Notification::create([
                        "userable_id" => $user->user_id,
                        "userable_type" => User::class,
                        "notifier_type_id" => NotifierEnum::ngo_submitted_register_form->value,
                        "message" => ""
                    ]);
                }
            }
            DB::commit();

            return response()->json(
                [
                    'message' => __('app_translation.success')
                ],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }
}
