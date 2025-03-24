<?php

namespace App\Jobs;

use App\Models\UserLoginLog;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserLoginJob implements ShouldQueue
{
    use Queueable;

    public $userAgent;
    public $userable_id;
    public $userable_type;
    public $ip_address;
    public $browser;
    public $device;
    /**
     * Create a new job instance.
     */
    public function __construct(
        $userAgent,
        $userable_id,
        $userable_type,
        $ip_address,
        $browser,
        $device,
    ) {
        $this->userAgent = $userAgent;
        $this->userable_id = $userable_id;
        $this->userable_type = $userable_type;
        $this->ip_address = $ip_address;
        $this->browser = $browser;
        $this->device = $device;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        UserLoginLog::create([
            'userable_id' => $this->userable_id,
            'userable_type' => $this->userable_type,
            'ip_address' => $this->ip_address,
            'browser' => $this->browser,
            'device' => $this->device,
        ]);
    }
}
