<?php
namespace App\Http\Controllers\api\app\file;

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
    public function uploadFile(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            return $this->saveFile($save->getFile(), $request);
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
    protected function saveFile(UploadedFile $file, Request $request)
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
        $pending = $request->pending_id ?? $this->pending($request);

        $data = [
            "pending_id" => $pending->id,
            "name" => $fileActualName,
            "size" => $fileSize,
            "extension" => $extension,
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
    protected function pending(Request $request)
    {
        $user = $request->user();

        PendingTask::where([
            "task_type" => $request->task_type,
            "user_id" => $user->id,
            "user_type" => $user->role_id,
        ])->delete();

        return PendingTask::create([
            "task_type" => $request->task_type,
            "content" => "",
            "user_id" => $user->id,
            "user_type" => $user->role_id,
        ]);
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
