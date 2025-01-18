<?php

namespace App\Http\Controllers\api\template;

use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function show($storage, $folder, $filename)
    {
        $path = storage_path('app/' . "{$storage}/{$folder}/{$filename}");
        if (!file_exists($path)) {
            return response()->json("File Not found");
        }
        return response()->file($path);
    }
    public function downloadDoc($storage, $folder, $folderType, $filename)
    {
        $path = storage_path('app/' . "{$storage}/{$folder}/{$folderType}/{$filename}");
        if (!file_exists($path)) {
            return response()->json("Document Not found");
        }

        return response()->file($path);
    }
}
