<?php

namespace Database\Seeders;

use App\Enums\LanguageEnum;
use App\Enums\NotifierEnum;
use App\Models\CheckListTrans;
use Illuminate\Database\Seeder;
use App\Models\NotifierType;
use App\Models\NotifierTypeTrans;

class NotifierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->notifierType();
    }

    protected function notifierType()
    {
        $type = NotifierType::create([
            "id" => NotifierEnum::ngo_submitted_register_form->value,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "NGO Submitted Signed Register Form.",
            'language_name' => LanguageEnum::default,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => 'موسسه فرم ثبت نام امضا شده را ارسال کرد.',
            'language_name' => LanguageEnum::farsi,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "موسسه لاسلیک شوی د نوم لیکنې فورمه واستوله.",
            'language_name' => LanguageEnum::pashto,
        ]);
        $type = NotifierType::create([
            "id" => NotifierEnum::ngo_register_form_accepted->value,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "Your register form is accepted.",
            'language_name' => LanguageEnum::default,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => 'فرم ثبت نام شما پذیرفته می شود.',
            'language_name' => LanguageEnum::farsi,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "ستاسو د نوم لیکنې فورمه ومنل شوه.",
            'language_name' => LanguageEnum::pashto,
        ]);
    }
}
