<?php

namespace Database\Seeders;

use App\Enums\CheckList\CheckListEnum;
use App\Enums\CheckListTypeEnum;
use App\Enums\LanguageEnum;
use App\Enums\RoleEnum;
use App\Models\CheckList;
use App\Models\CheckListTrans;
use App\Models\CheckListType;
use App\Models\CheckListTypeTrans;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CheckListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->CheckListType();
        $this->ngoRegisterationCheckList();
        $this->projectCheckList();
    }

    protected function CheckListType()
    {
        $checklist = CheckListType::create([
            'id' => CheckListTypeEnum::ngo_registeration,
        ]);
        CheckListTypeTrans::create([
            'value' => "NGO Register",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::default,
        ]);

        CheckListTypeTrans::create([
            'value' => "ثبت موسسه",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTypeTrans::create([
            'value' => "د موسسې ثبتول",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::pashto,
        ]);
        // Project
        $checklist = CheckListType::create([
            'id' => CheckListTypeEnum::project_registeration,
        ]);
        CheckListTypeTrans::create([
            'value' => "Project Register",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTypeTrans::create([
            'value' => "ثبت پروژه",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTypeTrans::create([
            'value' => "د پروژې راجستر",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::pashto,
        ]);
        $checklist = CheckListType::create([
            'id' => CheckListTypeEnum::ngo_agreement_extend,
        ]);
        CheckListTypeTrans::create([
            'value' => "Agreement extend",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTypeTrans::create([
            'value' => "تمدید توافقنامه",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTypeTrans::create([
            'value' => "د تړون نوي کول",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::pashto,
        ]);
        $checklist = CheckListType::create([
            'id' => CheckListTypeEnum::project_extend,
        ]);
        CheckListTypeTrans::create([
            'value' => "Project extend",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTypeTrans::create([
            'value' => "تمدید پروژه",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTypeTrans::create([
            'value' => "د پروژې غځول",
            'check_list_type_id' => $checklist->id,
            'language_name' => LanguageEnum::pashto,
        ]);
    }

    protected function ngoRegisterationCheckList()
    {
        $checklist = CheckList::create([
            'id' => CheckListEnum::director_nid,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 2048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "NGO Director National Identity or Passport",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "تذکره یا پاسپورت رئیس موسسه",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "د موسسه د رئیس تذکره یا پاسپورټ",
            'language_name' => LanguageEnum::pashto,
        ]);
        // 1.
        $checklist = CheckList::create([
            "id" => CheckListEnum::director_work_permit,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 2048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Director Work Permit",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "جواز کار رئیس",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "د رئیس کار جواز",
            'language_name' => LanguageEnum::pashto,
        ]);
        // 2.
        $checklist = CheckList::create([
            'id' => CheckListEnum::ministry_of_economy_work_permit,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 2048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Ministry of Economic Work Permit",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "جواز کار وزارت اقتصاد",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "د اقتصاد وزارت څخه د کار جواز",
            'language_name' => LanguageEnum::pashto,
        ]);
        // 3.


        $checklist = CheckList::create([
            'id' => CheckListEnum::articles_of_association,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 2048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Articles of Association",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "اساس نامه",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "اساس نامه",
            'language_name' => LanguageEnum::pashto,
        ]);
        // 4.
        $checklist = CheckList::create([
            "id" => CheckListEnum::ngo_representor_letter,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 2048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Representative introduction letter",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "مکتوب معرفی نمایده",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "د نماینده د معرفي لیک",
            'language_name' => LanguageEnum::pashto,
        ]);
        //5.
        $checklist = CheckList::create([
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 2048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "NGO Structure",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "تشکیلات موسسه",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "د موسسه جوړښت",
            'language_name' => LanguageEnum::pashto,
        ]);
        // 5.
        $checklist = CheckList::create([
            'id' => CheckListEnum::ngo_register_form_en,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 4048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Signed Registration Form (English)",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "فرم ثبت نام امضا شده (انگلیسی)",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "لاسلیک شوی د نوم لیکنې فورمه (انګلیسي)",
            'language_name' => LanguageEnum::pashto,
        ]);
        // 6.
        $checklist = CheckList::create([
            'id' => CheckListEnum::ngo_register_form_ps,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 4048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Signed Registration Form (Pashto)",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "فرم ثبت نام امضا شده (پشتو)",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "لاسلیک شوی د نوم لیکنې فورمه (پشتو)",
            'language_name' => LanguageEnum::pashto,
        ]);
        // 7.
        $checklist = CheckList::create([
            'id' => CheckListEnum::ngo_register_form_fa,
            'check_list_type_id' => CheckListTypeEnum::ngo_registeration,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 4048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Signed Registration Form (Farsi)",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "فرم ثبت نام امضا شده (فارسی)",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "لاسلیک شوی د نوم لیکنې فورمه (فارسی)",
            'language_name' => LanguageEnum::pashto,
        ]);
    }
    protected function projectCheckList()
    {
        $items = [
            [
                'id' => CheckListEnum::moe_project_introduction_letter,
                'value_default' => 'Project introduction letter from ministry of economy',
                'value_farsi' => 'مکتوب معرفی پروژه از وزارت اقتصاد',
                'value_pashto' => 'د اقتصاد وزارت له خوا د پروژې د معرفي کولو لیک',
                'file_size' => 2048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
            [
                'id' => CheckListEnum::project_articles_of_association,
                'value_default' => 'Articles of Association',
                'value_farsi' => 'اساس نامه',
                'value_pashto' => 'اساس نامه',
                'file_size' => 2048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
            [
                'id' => CheckListEnum::project_presentation,
                'value_default' => 'Project presentation',
                'value_farsi' => 'پرزنتیشن پروژه',
                'value_pashto' => 'د پروژې پریزنټیشن',
                'file_size' => 2048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
            [
                'id' => CheckListEnum::ngo_and_donor_contract,
                'value_default' => 'NGO & Donor Contract',
                'value_farsi' => 'قرارداد موسسه و دونر',
                'value_pashto' => ' موسسه او دونر قرارداد',
                'file_size' => 2048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
            [
                'id' => CheckListEnum::mou_en,
                'value_default' => 'Memorandum of Understanding (English)',
                'value_farsi' => 'تفاهم نامه (انگلیسی)',
                'value_pashto' => 'تفاهم نامه (انگلیسی)',
                'file_size' => 4048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
            [
                'id' => CheckListEnum::mou_fa,
                'value_default' => 'Memorandum of Understanding (Farsi)',
                'value_farsi' => 'تفاهم نامه (فارسی)',
                'value_pashto' => 'تفاهم نامه (فارسی)',
                'file_size' => 4048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
            [
                'id' => CheckListEnum::mou_ps,
                'value_default' => 'Memorandum of Understanding (Pashto)',
                'value_farsi' => 'تفاهم نامه (پشتو)',
                'value_pashto' => 'تفاهم نامه (پشتو)',
                'file_size' => 4048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
            [
                'id' => CheckListEnum::project_ministry_of_economy_work_permit,
                'value_default' => 'Project work permit from  ministry of economy',
                'value_farsi' => 'جواز کار پروژه از وزارت اقتصاد',
                'value_pashto' => 'د اقتصاد وزارت څخه د پروژې د کار جواز',
                'file_size' => 4048,
                'acceptable_extensions' => 'pdf,jpeg,png,jpg',
                'acceptable_mimes' => 'application/pdf,image/jpeg,image/png,image/jpg',
                'accept' => '.pdf,.jpeg,.png,.jpg',
            ],
        ];

        foreach ($items as $item) {
            $checklist = CheckList::create([
                'id' => $item['id'],
                'check_list_type_id' => CheckListTypeEnum::project_registeration,
                'acceptable_extensions' => $item['acceptable_extensions'],
                'acceptable_mimes' => $item['acceptable_mimes'],
                'accept' => $item['accept'],
                'description' => '',
                'file_size' => $item['file_size'],
                'user_id' => RoleEnum::super,
            ]);

            CheckListTrans::create([
                'check_list_id' => $checklist->id,
                'value' => $item['value_default'],
                'language_name' => LanguageEnum::default,
            ]);

            CheckListTrans::create([
                'check_list_id' => $checklist->id,
                'value' => $item['value_farsi'],
                'language_name' => LanguageEnum::farsi,
            ]);

            CheckListTrans::create([
                'check_list_id' => $checklist->id,
                'value' => $item['value_pashto'],
                'language_name' => LanguageEnum::pashto,
            ]);
        }
    }
}
