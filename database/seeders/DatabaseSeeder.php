<?php

namespace Database\Seeders;

use App\Models\Gender;
use App\Models\Status;
use App\Models\NgoType;
use App\Models\NidType;
use App\Models\Setting;
use App\Enums\StaffEnum;
use App\Models\Language;
use App\Models\NewsType;
use App\Models\Priority;
use App\Models\TimeUnit;
use App\Models\Translate;
use App\Enums\SettingEnum;
use App\Models\StatusType;
use App\Enums\PriorityEnum;
use App\Enums\TimeUnitEnum;
use App\Models\NgoTypeTrans;
use App\Models\NidTypeTrans;
use App\Models\SettingTrans;
use App\Models\NewsTypeTrans;
use App\Models\PriorityTrans;
use App\Enums\Type\NgoTypeEnum;
use Illuminate\Database\Seeder;
use App\Enums\Status\StatusEnum;
use App\Enums\Type\StatusTypeEnum;
use App\Models\TimeUnitTrans;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CountrySeeder;
use Database\Seeders\CheckListSeeder;

/*
1. If you add new Role steps are:
    1. Add to following:
        - RoleEnum
        - RoleSeeder
        - RolePermissionSeeder (Define which permissions role can access)
        - Optional: Set Role on User go to JobAndUserSeeder Then UserPermissionSeeder


2. If you add new Permission steps are:
    1. Add to following:
        - PermissionEnum
        - SubPermissionEnum (In case has Sub Permissions)
        - PermissionSeeder
        - SubPermissionSeeder Then SubPermissionEnum (I has any sub permissions) 
        - RolePermissionSeeder (Define Which Role can access the permission)
        - Optional: Set Permission on User go to JobAndUserSeeder Then UserPermissionSeeder

        
3. If you add new Sub Permission steps are:
    1. Add to following:
        - SubPermissionEnum
        - SubPermissionSeeder
        - RolePermissionSeeder (Define Which Role can access the permission)
        - Optional: Set Permission on User go to JobAndUserSeeder Then UserPermissionSeeder
*/

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->languages();
        $this->gender();
        $this->call(CountrySeeder::class);
        $this->call(DestinationSeederSecond::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SubPermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->statusType();
        $this->status();
        $this->call(JobAndUserSeeder::class);
        $this->call(CheckListSeeder::class);
        $this->call(UserPermissionSeeder::class);
        $this->call(NotifierSeeder::class);

        $this->ngoTypes();
        $this->settings();
        $this->newsTypes();
        $this->priorityTypes();
        $this->nidTypes();
        $this->staffTypes();
        $this->call(AboutSeeder::class);
        $this->call(ApprovalSeeder::class);
    }
    public function ngoTypes()
    {
        $international = NgoType::factory()->create([
            'id' => NgoTypeEnum::International,
        ]);
        NgoTypeTrans::factory()->create([
            "value" => "بین المللی",
            "language_name" => "fa",
            "ngo_type_id" => $international->id
        ]);
        NgoTypeTrans::factory()->create([
            "value" => "نړیوال",
            "language_name" => "ps",
            "ngo_type_id" => $international->id
        ]);
        NgoTypeTrans::factory()->create([
            "value" => "International",
            "language_name" => "en",
            "ngo_type_id" => $international->id
        ]);

        $intergovernmental = NgoType::factory()->create([
            'id' => NgoTypeEnum::Intergovernmental,

        ]);
        NgoTypeTrans::factory()->create([
            "value" => "بین الدولتی",
            "language_name" => "fa",
            "ngo_type_id" => $intergovernmental->id
        ]);

        NgoTypeTrans::factory()->create([
            "value" => "بین الدولتی",
            "language_name" => "ps",
            "ngo_type_id" => $intergovernmental->id
        ]);
        NgoTypeTrans::factory()->create([
            "value" => "Intergovernmental",
            "language_name" => "en",
            "ngo_type_id" => $intergovernmental->id
        ]);

        $domestic = NgoType::factory()->create([
            'id' => NgoTypeEnum::Domestic,

        ]);
        NgoTypeTrans::factory()->create([
            "value" => "داخلی",
            "language_name" => "fa",
            "ngo_type_id" => $domestic->id
        ]);
        NgoTypeTrans::factory()->create([
            "value" => "کورني",
            "language_name" => "ps",
            "ngo_type_id" => $domestic->id
        ]);
        NgoTypeTrans::factory()->create([
            "value" => "Domestic",
            "language_name" => "en",
            "ngo_type_id" => $domestic->id
        ]);
    }
    public function newsTypes()
    {
        $newsType = NewsType::create([]);
        NewsTypeTrans::create([
            "value" => "اخبار صحی",
            "language_name" => "fa",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "روغتیا خبرونه",
            "language_name" => "ps",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "Health News",
            "language_name" => "en",
            "news_type_id" => $newsType->id
        ]);

        $newsType = NewsType::create([]);
        NewsTypeTrans::create([
            "value" => "اخبار جهان",
            "language_name" => "fa",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "نړیوال خبرونه",
            "language_name" => "ps",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "International News",
            "language_name" => "en",
            "news_type_id" => $newsType->id
        ]);
    }
    public function priorityTypes()
    {
        $priority = Priority::create([
            'id' => PriorityEnum::high->value
        ]);
        PriorityTrans::create([
            "value" => "اولویت بالا",
            "language_name" => "fa",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "لوړ لومړیتوب",
            "language_name" => "ps",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "High Priority",
            "language_name" => "en",
            "priority_id" => $priority->id
        ]);
        $priority = Priority::create([
            'id' => PriorityEnum::medium->value
        ]);
        PriorityTrans::create([
            "value" => "اولویت متوسط",
            "language_name" => "fa",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "منځنی لومړیتوب",
            "language_name" => "ps",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "Medium Priority",
            "language_name" => "en",
            "priority_id" => $priority->id
        ]);
        $priority = Priority::create([
            'id' => PriorityEnum::low->value
        ]);
        PriorityTrans::create([
            "value" => "اولویت پایین",
            "language_name" => "fa",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "ټیټ لومړیتوب",
            "language_name" => "ps",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "Low Priority",
            "language_name" => "en",
            "priority_id" => $priority->id
        ]);
    }
    public function nidTypes()
    {
        $nid = NidType::create([]);
        NidTypeTrans::create([
            "value" => "پاسپورت",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "پاسپورټ",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "Passport",
            "language_name" => "en",
            "nid_type_id" => $nid->id
        ]);
        $nid = NidType::create([]);
        NidTypeTrans::create([
            "value" => "تذکره",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "تذکره",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "ID card",
            "language_name" => "en",
            "nid_type_id" => $nid->id
        ]);
    }
    public function statusType()
    {
        $statustype =  StatusType::factory()->create([
            'id' => StatusTypeEnum::ngo,
        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'NGO'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'موسسه'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'موسسه'

        ]);

        $statustype =  StatusType::factory()->create([
            'id' => StatusTypeEnum::donor,
        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Donor'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'دونر'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'دونر'

        ]);

        $statustype =  StatusType::factory()->create([
            'id' => StatusTypeEnum::project,
        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Project'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'حالت پروژه'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'پروژی'

        ]);

        $statustype = StatusType::factory()->create([
            'id' => StatusTypeEnum::agreement,
        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Agreement'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'تفاهم نامه '

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'تړون لیک '

        ]);

        $statustype = StatusType::factory()->create([
            'id' => StatusTypeEnum::general,
        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'General'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'عمومی'

        ]);
        DB::table('status_type_trans')->insert([
            'status_type_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'عمومی'

        ]);
    }
    public function status()
    {
        $statustype =  Status::create([
            'id' => StatusEnum::active,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Active'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'فعال'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'فعال'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::block,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Block'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'مسدود'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'مسدود'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::document_upload_required,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Document upload required'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'آپلود مدرک الزامی است'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'د سند اپلوډ اړین دی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::pending_approval,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Pending approval'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'در انتظار تأیید'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'د تصویب په تمه'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::rejected,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Rejected'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'رد شده'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'رد شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::expired,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Expired'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'تمام شده'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'ختم شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::extended,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Extended'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'تمدید شده'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'غځول شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::approved,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Approved'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'تایید شده'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'منظور شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::registered,
            'status_type_id' => StatusTypeEnum::general->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Registered'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'ثبت شده'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'ثبت شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::registration_incomplete,
            'status_type_id' => StatusTypeEnum::ngo->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Registration incomplete'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'ثبت نام ناتمام است.'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'نوم لیکنه نیمګړې ده.'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::waiting_for_project_schedule,
            'status_type_id' => StatusTypeEnum::project->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Waiting for project schedule'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'پروژه منتظر زمانبندی است'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'پروژه د مهالویش په تمه ده.'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::project_has_comment,
            'status_type_id' => StatusTypeEnum::project->value
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Project has comment'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'پروژه نظرات دارد'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'پروژه تبصره لري'
        ]);
    }
    public function staffTypes()
    {
        DB::table('staff_types')->insert([
            'id' => StaffEnum::manager,
            'name' => "manager",
        ]);
        DB::table('staff_types')->insert([
            'id' => StaffEnum::director,
            'name' => "director",
        ]);
        DB::table('staff_types')->insert([
            'id' => StaffEnum::technical_support,
            'name' => "technical_support",
        ]);
    }
    public function settings()
    {
        $unit = TimeUnit::factory()->create([
            "id" => TimeUnitEnum::day->value,
        ]);
        TimeUnitTrans::factory()->create([
            "time_unit_id" => $unit->id,
            "value" => "Day",
            "language_name" => "en",
        ]);
        TimeUnitTrans::factory()->create([
            "time_unit_id" => $unit->id,
            "value" => "روز",
            "language_name" => "fa",
        ]);
        TimeUnitTrans::factory()->create([
            "time_unit_id" => $unit->id,
            "value" => "ورځ",
            "language_name" => "ps",
        ]);
        $setting = Setting::factory()->create([
            "id" => SettingEnum::registeration_expire_time->value,
            "value" => "365", // days,
            "time_unit_id" => TimeUnitEnum::day->value
        ]);
        SettingTrans::factory()->create([
            "setting_id" => $setting->id,
            "value" => "Registeration Expiration Deadline",
            "language_name" => "en",
        ]);
        SettingTrans::factory()->create([
            "setting_id" => $setting->id,
            "value" => "مهلت ثبت نام",
            "language_name" => "fa",
        ]);
        SettingTrans::factory()->create([
            "setting_id" => $setting->id,
            "value" => "د نوم لیکنې د پای نیټه",
            "language_name" => "ps",
        ]);
    }
    protected function gender()
    {

        Gender::create([
            'name_en' => 'Male',
            'name_fa' => 'مرد',
            'name_ps' => 'نارینه'
        ]);

        Gender::create([
            'name_en' => 'Famale',
            'name_fa' => 'زن',
            'name_ps' => 'ښځینه'
        ]);
    }
    public function languages(): void
    {
        Language::factory()->create([
            "name" => "en"
        ]);
        Language::factory()->create([
            "name" => "ps"
        ]);
        Language::factory()->create([
            "name" => "fa"
        ]);
    }
    public function Translate($value, $language, $translable_id, $translable_type): void
    {
        Translate::factory()->create([
            "value" => $value,
            "language_name" => $language,
            "translable_type" => $translable_type,
            "translable_id" => $translable_id,
        ]);
    }
}
