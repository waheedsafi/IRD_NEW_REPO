<?php

namespace App\Http\Controllers\api\app\file;

use App\Http\Controllers\Controller;
use App\Models\CheckList;
use App\Models\News;
use App\Models\NewsDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileController extends Controller
{
    //
    // documentation from this site https://shouts.dev/articles/laravel-upload-large-file-with-resumablejs-and-laravel-chunk-upload
    public function fileUpload(Request $request)
    {
        $request->validate([
            'check_list_id' => 'required|integer',
            'file' => [
                'required',
                'file',
                function ($attribute, $value, $fail) use ($request) {
                    $checkListId = $request->input('check_list_id');

                    // Fetch allowed extensions based on `check_list_id`.
                    $allowedExtensions = CheckList::find($checkListId)?->file_extensions;
                    // Assume file_extensions is an array, e.g., ['pdf', 'docx']

                    if (!$allowedExtensions || !in_array($value->getClientOriginalExtension(), $allowedExtensions)) {
                        $fail("The $attribute must be a file of type: " . implode(', ', $allowedExtensions) . '.');
                    }
                },
            ],
        ]);

        // create the file receiver

        $checklist =  CheckList::find($request->check_list_id);

        $checklist_name =   $checklist->name;
        $userId = Auth::user()->id;
        $path =  "app/private/temp/" . $userId . '/' . $checklist_name;

        return $this->uploadChunk($request, $path);
    }

    protected function uploadChunk($request, $path)
    {
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
            return $this->saveFile($save->getFile(), $path);
        }

        // we are in chunk mode, lets send the current progress
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    protected function saveFile(UploadedFile $file, $path)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $filename . "_" . md5(time()) . "." . $extension;

        // Ensure directory exists
        $finalPath = storage_path($path);
        if (!file_exists($finalPath)) {
            mkdir($finalPath, 0777, true);
        }

        // Save the file
        $file->move($finalPath, $fileName);

        return [
            'path' => asset("storage/$path/$fileName"),
            'name' => $fileName,
            'original_name' => $filename,
            'mime' => $extension,
        ];
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
