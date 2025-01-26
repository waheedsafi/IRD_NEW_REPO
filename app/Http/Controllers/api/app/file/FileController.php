<?php

namespace App\Http\Controllers\api\app\file;

use App\Models\News;
use App\Models\CheckList;
use App\Models\NewsDocument;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

class FileController extends Controller
{
    //
    // documentation from this site https://shouts.dev/articles/laravel-upload-large-file-with-resumablejs-and-laravel-chunk-upload

    public function fileUpload(Request $request)
    {
        $checklist = CheckList::find($request->check_list_id);

        $checklist_name = $checklist->name;
        $userId = $request->user()->id;

        // Define the storage path
        $path = "app/private/temp/" . $userId . '/' . $checklist_name;

        // Ensure the destination directory exists
        $fullPath = storage_path('app/' . $path);
        if (!file_exists($fullPath)) {
            // Create the directory with 0755 permissions, and allow subdirectories
            mkdir($fullPath, 0755, true);
        }

        return $this->uploadChunk($request, $path);
    }

    protected function uploadChunk(Request $request, $path)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // Check if the upload is successful, otherwise throw exception
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // Receive the file chunk
        $save = $receiver->receive();

        // Check if the upload has finished
        if ($save->isFinished()) {
            // Save the file to the specified path using Storage facade
            return $this->saveFile($save->getFile(), $path);
        }

        // We are in chunk mode, let's send the current progress
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    protected function saveFile($file, $path)
    {
        // Get the original file name
        $fileName = $file->getClientOriginalName();

        // Save the file using Laravel's Storage facade to the specified path
        $destinationPath = $path . '/' . $fileName;

        // Store the file using Storage facade
        Storage::disk('local')->put($destinationPath, file_get_contents($file->getPathname()));

        // Return a response indicating success and the file's final location
        return response()->json([
            'status' => 'success',
            'file_path' => storage_path('app/' . $destinationPath)
        ]);
    }



    public function newsFileUpload(Request $request)
    {


        $request->validate([

            'file' => 'required|mimes:pdf,png,jpeg,jpg,mp4,mkv'
        ]);
        $news = $request->news_id
            ? News::findOrFail($request->news_id)
            : News::create([
                'news_type_id' => 1,
                'priority_id' => 1,
                'visible' => 0,
                'user_id' => Auth::id(),
                'expiry_date' => '0001-01-01',
                'submited' => 0,
            ]);

        $newsId = $news->id;

        $userId = Auth::user()->id;

        $path =  "app/public/news/" . $userId . '/' . $newsId;

        $data = $this->uploadChunk($request, $path);


        NewsDocument::create([

            'news_id' => $newsId,
            'path' => $data['path'],
            'extintion' => $data['mime'],

        ]);

        return response()->json(
            [
                'message' => __('app_translation.success'),
                'news_id' => $newsId

            ],
            200
        );
    }
}
