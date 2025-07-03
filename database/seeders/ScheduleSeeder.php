<?php

namespace Database\Seeders;

use App\Models\ScheduleStatus;
use App\Models\ScheduleStatusTrans;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->statuses();
    }
    public function statuses()
    {
        $item = ScheduleStatus::factory()->create([]);
        ScheduleStatusTrans::factory()->create([
            "value" => "Scheduled",
            "language_name" => "en",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "تعیین شده",
            "language_name" => "fa",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "مهالویش شوی",
            "language_name" => "ps",
            "schedule_status_id" => $item->id
        ]);
        $item = ScheduleStatus::factory()->create([]);
        ScheduleStatusTrans::factory()->create([
            "value" => "Cancelled",
            "language_name" => "en",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "لغو شده",
            "language_name" => "fa",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "لغوه شوی",
            "language_name" => "ps",
            "schedule_status_id" => $item->id
        ]);
        $item = ScheduleStatus::factory()->create([]);
        ScheduleStatusTrans::factory()->create([
            "value" => "Postponed",
            "language_name" => "en",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "به تعویق افتاده",
            "language_name" => "fa",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "ځنډول شوی",
            "language_name" => "ps",
            "schedule_status_id" => $item->id
        ]);
        $item = ScheduleStatus::factory()->create([]);
        ScheduleStatusTrans::factory()->create([
            "value" => "Completed",
            "language_name" => "en",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "تکمیل شده",
            "language_name" => "fa",
            "schedule_status_id" => $item->id
        ]);
        ScheduleStatusTrans::factory()->create([
            "value" => "بشپړ شوی",
            "language_name" => "ps",
            "schedule_status_id" => $item->id
        ]);
    }
}
