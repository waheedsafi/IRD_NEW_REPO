<?php

namespace App\Repositories\Storage;

interface StorageRepositoryInterface
{
    /**
     * Creates a approval.
     * 
     *
     * @param string agreement_id
     * @param string ngo_id
     * @param string pending_task_id
     * @param callable callback
     * @return boolean
     */
    public function documentStore($agreement_id, $ngo_id, $pending_task_id, ?callable $callback);
    public function projectDocumentStore($project_id, $ngo_id, $pending_task_id, ?callable $callback);
    public function scheduleDocumentStore($schedule_id, $pending_task_id, ?callable $callback);
}
