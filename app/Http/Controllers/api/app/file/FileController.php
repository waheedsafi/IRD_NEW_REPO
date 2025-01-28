<?php

namespace App\Http\Controllers\api\app\file;

use App\Models\News;
use App\Models\CheckList;
use Illuminate\Support\Str;
use App\Models\NewsDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use App\Models\PendingTask;
use App\Models\PendingTaskDocument;
use Illuminate\Support\Facades\Auth;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

class FileController extends Controller
{

    /**
     * Handles the file upload
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws UploadMissingFileException
     * @throws UploadFailedException
     */
    public function uploadFile(Request $request)
    {
        $this->checkListCheck($request);
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile(), $request);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return JsonResponse
     */
    protected function saveFile(UploadedFile $file, Request $request)
    {

        $fileActualName = $file->getClientOriginalName();
        $fileName = $this->createFilename($file);

        $fileSize = $file->getSize();
        // move the file name
        $finalPath = $this->getTempPath();
        $file->move($finalPath, $fileName);
        $extension = $file->getClientOriginalExtension();
        $pending = $request->pending_id ?? $this->pending($request);

        $data = [
            'pending_id' => $pending->id,
            'name' => $fileActualName,
            'size' => $fileSize,
            "extension" => $extension,
        ];

        $this->pendingDocument($data);
        return response()->json([
            $data
        ]);
    }
    public function pending($request)
    {
        $user_id  =  $request->user()->id;
        $role_id =  $request->user()->role_id;
        $task_type =  $request->task_type;

        PendingTask::where('task_type', $task_type)
            ->where('user_id', $user_id)
            ->where('user_type', $role_id)
            ->delete();

        $pending =  PendingTask::create([
            'task_type' => $task_type,
            'content' => '',
            'user_id' => $user_id,
            'user_type' => $role_id

        ]);
        return $pending;
    }

    public function pendingDocument($data)
    {

        PendingTaskDocument::create([
            'pending_task_id' => $data['pending_id'],
            'size' => $data['size'],
            'path' => $data['path'],
            'actual_name' => $data['name'],
            'extension' => $data['extension']

        ]);
    }
    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */

    public function checkListCheck($request)
    {
        $checklist_id = $request->checklist_id;

        // Fetch the checklist, handling the case where it's not found
        $checklist = CheckList::find($checklist_id);
        if (!$checklist) {
            return response()->json(['error' => 'Checklist not found.'], 404);
        }

        // Validate the request
        $request->validate([
            'file' => [
                'required',
                "mimes:{$checklist->file_extensions}",
                "max:{$checklist->file_size}", // Laravel's file size uses kilobytes (KB)
            ],
        ]);
    }

    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        return  Str::uuid() . "." . $extension;
    }
}
