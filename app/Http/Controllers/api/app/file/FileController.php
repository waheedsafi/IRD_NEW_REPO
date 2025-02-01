<?php

namespace App\Http\Controllers\api\app\file;

use App\Enums\Type\TaskTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\CheckList;
use App\Models\PendingTask;
use App\Models\PendingTaskDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileController extends Controller
{
    /**
     * Handles the file upload.
     */
    public function uploadNgoFile(Request $request, $ngo_id)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = TaskTypeEnum::ngo_registeration;
            return $this->saveFile($save->getFile(), $request, $ngo_id, $task_type);
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }

    public function uploadProjectFile(Request $request, $project_id)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = TaskTypeEnum::project_registeration;

            return $this->saveFile($save->getFile(), $request, $project_id, $task_type);
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }


    /**
     * Saves the file and validates it.
     */
    protected function saveFile(UploadedFile $file, Request $request, $id, $task_type)
    {
        $fileActualName = $file->getClientOriginalName();
        $fileName = $this->createFilename($file);
        $fileSize = $file->getSize();
        $finalPath = $this->getTempPath();
        $fileFullPath = "{$finalPath}/{$fileName}";

        $file->move($finalPath, $fileName);

        // Validate the file against checklist rules
        $validationResult = $this->checkListCheck($request, $fileFullPath);

        if ($validationResult !== true) {
            return $validationResult; // Return validation errors
        }

        // Process pending task and document creation
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $pending =  $this->pending($request, $id, $task_type);

        $data = [
            "pending_id" => $pending,
            "name" => $fileActualName,
            "size" => $fileSize,
            "check_list_id" => $request->check_list_id,
            "extension" => $mimeType,
            "path" => $fileFullPath,
        ];





        $this->pendingDocument($data);

        return response()->json($data, 201);
    }

    /**
     * Validate file using checklist settings.
     */
    public function checkListCheck($request, $filePath)
    {
        $checklist = CheckList::find($request->checklist_id);

        if (!$checklist) {
            return response()->json(["error" => "Checklist not found."], 404);
        }

        $rules = [
            "file" => [
                "required",
                "mimes:{$checklist->file_extensions}",
                "max:{$checklist->file_size}",
            ],

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Storage::delete($filePath); // Cleanup invalid file
            return response()->json(["errors" => $validator->errors()], 422);
        }

        return true;
    }

    /**
     * Create or retrieve pending task.
     */
    protected function pending(Request $request, $id, $task_type)
    {
        $user = $request->user();
        $user_id = $user->id;
        $role = $user->role_id;


        $task = PendingTask::where('user_id', $user_id)
            ->where('user_type', $role)
            ->where('task_type', $task_type)
            ->where('task_id', $id)
            ->first();

        return $task->id;
    }

    /**
     * Save pending task document.
     */
    protected function pendingDocument(array $data)
    {
        PendingTaskDocument::create([
            "pending_task_id" => $data["pending_id"],
            "size" => $data["size"],
            "path" => $data["path"],
            "check_list_id" => $data["check_list_id"],
            "actual_name" => $data["name"],
            "extension" => $data["extension"],
        ]);
    }

    /**
     * Generate a unique filename.
     */
    protected function createFilename(UploadedFile $file): string
    {
        return Str::uuid() . "." . $file->getClientOriginalExtension();
    }
}
