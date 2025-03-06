<?php

namespace App\Traits\Helper;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HelperTrait
{
    public function createChunkUploadFilename(UploadedFile $file)
    {
        return Str::uuid() . "." . $file->getClientOriginalExtension();
    }
    public function getTempFullPath()
    {
        return storage_path() . "/app/temp/";
    }
    public function getTempFilePath($fileName)
    {
        return "temp/{$fileName}";
    }

    public function tempFileExist($filePath)
    {
        return file_exists(storage_path() . "/app/{$filePath}");
    }

    public function deleteTempFile($filePath)
    {
        return unlink(storage_path() . "/app/{$filePath}");
    }
    public function ngoRegisterFolder($ngo_id, $agreement_id, $check_list_id)
    {
        return storage_path() . "/app/private/ngos/{$ngo_id}/register/{$agreement_id}/{$check_list_id}/";
    }
    public function ngoRegisterDBPath($ngo_id, $agreement_id, $check_list_id, $fileName)
    {
        return "private/ngos/{$ngo_id}/register/{$agreement_id}/{$check_list_id}/" . $fileName;
    }
}
