<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $token;
    public $record;
    /**
     * Create a new job instance.
     */
    public function __construct($token, $record)
    {
        $this->token = $token;
        $this->record = $record;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token, // Add the same token here
        ])->post('http://127.0.0.1:8001/api/v1/store/notification', [
            'record' => $this->record,
        ]);
    }
}
