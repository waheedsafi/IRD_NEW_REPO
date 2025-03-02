<?php

namespace App\Traits\File;

use App\Models\Document;
use App\Models\PendingTask;
use App\Models\PendingTaskDocument;

trait PendingFileTrait
{
    protected function singleChecklistDBDocStore($pending_id, $agreement_id, $ngo_id)
    {
        $task = PendingTask::where('id', $pending_id)
            ->first();

        if (!$task) {
            return [
                "error" => response()->json(['error' => __('app_translation.task_not_found')], 404),
                "success" => false,
                "no_task" => true
            ];
        }
        // Get checklist IDs

        $documents = PendingTaskDocument::select('size', 'path', 'check_list_id', 'actual_name', 'extension')
            ->where('pending_task_id', $task->id)
            ->first();

        $oldPath = storage_path("app/" . $documents->path); // Absolute path of temp file
        $newDirectory = storage_path() . "/app/private/ngos/{$ngo_id}/{$agreement_id}/{$documents->check_list_id}/";

        if (!file_exists($newDirectory)) {
            mkdir($newDirectory, 0775, true);
        }

        $newPath = $newDirectory . basename($documents->path); // Keep original filename

        $dbStorePath = "private/ngos/{$ngo_id}/{$agreement_id}/{$documents->check_list_id}/"
            . basename($documents->path);
        // Ensure the new directory exists

        // Move the file
        if (file_exists($oldPath)) {
            rename($oldPath, $newPath);
        } else {
            return [
                "error" => response()->json(['error' => __('app_translation.file_not_found')], 404),
                "success" => false,
                "no_task" => false
            ];
        }

        $document = Document::create([
            'actual_name' => $documents->actual_name,
            'size' => $documents->size,
            'path' => $dbStorePath,
            'type' => $documents->extension,
            'check_list_id' => $documents->check_list_id,
        ]);

        return [
            "document" => $document,
            "success" => true,
            "no_task" => false
        ];
    }
}
