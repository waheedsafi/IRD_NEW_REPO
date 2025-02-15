<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\DestinationType;
use Illuminate\Database\Seeder;
use App\Models\DestinationTrans;
use App\Enums\DestinationTypeEnum;
use App\Models\DestinationTypeTrans;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DestinationSeederSecond extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offic =  DestinationType::factory()->create([
            "id" => DestinationTypeEnum::muqam->value
        ]);
        DestinationTypeTrans::factory()->create([
            "value" => "Muqam",
            "destination_type_id" => $offic->id,
            "language_name" => "en",
        ]);
        DestinationTypeTrans::factory()->create([
            "value" => "مقام",
            "destination_type_id" => $offic->id,
            "language_name" => "fa",
        ]);
        DestinationTypeTrans::factory()->create([
            "value" => "مقام",
            "destination_type_id" => $offic->id,
            "language_name" => "ps",
        ]);
        $directorate =  DestinationType::factory()->create([
            "id" => DestinationTypeEnum::directorate->value
        ]);
        DestinationTypeTrans::factory()->create([
            "value" => "Directorate",
            "destination_type_id" => $directorate->id,
            "language_name" => "en",
        ]);
        DestinationTypeTrans::factory()->create([
            "value" => "ریاست",
            "destination_type_id" => $directorate->id,
            "language_name" => "fa",
        ]);
        DestinationTypeTrans::factory()->create([
            "value" => "ریاست",
            "destination_type_id" => $directorate->id,
            "language_name" => "ps",
        ]);

        $this->offic($offic);
        $this->destinations($directorate);
    }

    public function offic($offic): void
    {
        // Change destination types
        $destination = [
            "Deputy Ministry of Health Service Delivery" => [
                "fa" => " معینیت عرضه خدمات صحی",
                "ps" => "د روغتیايي خدمتونو وړاندې کولو معینیت",
            ],
            "Deputy Ministry of Medicine and Food" => [
                "fa" => "معینیت دوا و غذا  ",
                "ps" => "د حوړو او درملو معینیت",
            ],
            "Ministers Office" => [
                "fa" => "مقام وزارت ",
                "ps" => "د وزارت مقام",
            ],
            "Deputy Ministry of Finance and Administration" => [
                "fa" => " معینیت مالی و اداری ",
                "ps" => "د مالي او اداري چارو معینیت",
            ],
            "Deputy Ministry of Health Policy and Development" => [
                "fa" => "معینیت پالیسی و انکشاف صحت  ",
                "ps" => "د روغتیايي پراختیا او پالیسۍ معینیت",
            ],
        ];
        foreach ($destination as $name => $destinations) {
            // Create the country record
            $des =  Destination::factory()->create([
                "color" => "#B4D455",
                "destination_type_id" => $offic->id,
            ]);
            DestinationTrans::factory()->create([
                "value" => trim($name),
                "destination_id" => $des->id,
                "language_name" => "en",
            ]);
            // Loop through translations (e.g., fa, ps)
            foreach ($destinations as $key => $value) {
                DestinationTrans::factory()->create([
                    "value" => trim($value),
                    "destination_id" => $des->id,
                    "language_name" => $key,
                ]);
            }
        }
    }

    public function destinations($directorate): void
    {
        // Change destination types
        $destination = [

            "Directorate of Information Technology" => [
                "fa" => "ریاست تکنالوژی معلوماتی ",
                "ps" => "د معلوماتي ټکنالوژۍ ریاست",
            ],
            "General Directorate of Office, Documentation, and Communication" => [
                "fa" => "ریاست عمومی دفتر٬ اسناد و ارتباط",
                "ps" => "د ارتباطاتو، اسنادو او دفتر لوی ریاست",
            ],

            "Directorate of Information, Public Relations, and Spokesperson" => [
                "fa" => "ریاست اطلاعات٬ ارتباط عامه و سخنگو  ",
                "ps" => "د ارتباطاتو، عامه اړیکو او ویاندویۍ ریاست  ",
            ],

            "Directorate of preaching and Guidance " => [
                "fa" => " ریاست دعوت و ارشاد ",
                "ps" => "د ارشاد او دعوت ریاست  ",
            ],

            "Directorate of Internal Audit" => [
                "fa" => " ریاست تفتیش داخلی ",
                "ps" => "د داخلي پلتڼې ریاست",
            ],

            "General Directorate of Supervision and Inspection" => [
                "fa" => " ریاست عمومی نظارت و بازرسی ",
                "ps" => "د نظارت او ارزیابۍ لوی ریاست  ",
            ],

            "Directorate of Evaluation, Analysis, and Data Interpretation" => [
                "fa" => " ریاست ارزیابی ٬ تحلیل و تجزیه ارقام ",
                "ps" => "د ارقامو د تحلیل تجزیي او ارزیابۍ ریاست  ",
            ],

            "Directorate of Medicine and Food Inspection" => [
                "fa" => "ریاست نظارت و بازرسی از ادویه و مواد غذایی",
                "ps" => "د خوړو او درملو د نظارت او ارزیابۍ ریاست ",
            ],

            "Directorate of Health Service Delivery Inspection" => [
                "fa" => " ریاست نظارت و بازرسی ازعرضه خدمات صحی ",
                "ps" => "  د روغتیايي خدمتونو څخه د نظارت او ارزیابۍ ریاست",
            ],

            "Directorate of Health Facility Assessment" => [
                "fa" => " ریاست بررسی از تاسیسات صحی  ",
                "ps" => "د روغتیايي تاسیساتو د څېړنې ریاست  ",
            ],

            "Directorate of International Relations, Coordination, and Aid Management" => [
                "fa" => "ریاست روابط بین المللی٬ هماهنگی وانسجام کمکها ",
                "ps" => " ریاست روابط بین المللی٬ هماهنگی وانسجام کمکها ",
            ],

            "General Directorate of the Medical Council" => [
                "fa" => " ریاست عمومی شورای طبی ",
                "ps" => " د طبي شورا لوی ریاست  ",
            ],

            "Directorate of Medical Ethics and Standards Promotion" => [
                "fa" => " ریاست اخلاق طبابت و ترویج استندرد ها ",
                "ps" => "د معیارونو د پلي کولو او  طبي اخلاقو ریاست  ",
            ],

            "Directorate of Regulation for Nurses, Midwives, and Other Medical Personnel" => [
                "fa" => " ریاست تنظیم امور نرسها٬قابله ها وسایر پرسونل طبی",
                "ps" => "د نرسانو، قابله ګانو او ورته نورو طبي کارکوونکو د چارو د ترتیب ریاست ",
            ],

            "Directorate of Licensing and Registration for Doctors and Health Personnel" => [
                "fa" => "ریاست ثبت و صدور جواز فعالیت امور دوکتوران و سایر پرسونل صحی ",
                "ps" => "د روغتیايي کارکوونکو او ورته نور طبي پرسونل د فعالیت جوازونو د ثبت او صدور ریاست ",
            ],

            "Directorate of Provincial Health Coordination" => [
                "fa" => "ریاست هماهنگی صحت ولایات ",
                "ps" => "د ولایتونو د روغتیا همغږۍ ریاست ",
            ],

            "General Directorate of Curative Medicine" => [
                "fa" => "ریاست عمومی طب معالجوی  ",
                "ps" => "د معالجوي طب لوی ریاست",
            ],

            "Directorate of Diagnostic Services" => [
                "fa" => " ریاست خدمات تشخیصیه",
                "ps" => "د تشخیصیه خدماتو ریاست",
            ],

            "Directorate of National Addiction Treatment Program" => [
                "fa" => "ریاست برنامه ملی تداوی معتادین ",
                "ps" => "د روږدو درملنې ملي برنامې ریاست",
            ],

            "General Directorate of Preventive Medicine and Disease Control" => [
                "fa" => "ریاست عمومی طب وقایه و کنترول امراض ",
                "ps" => "د ناروغیو د مخنیوي او کنټرول لوی ریاست",
            ],

            "Directorate of Primary Health Care (PHC)" => [
                "fa" => " ریاست مراقبتهای صحی اولیهPHC",
                "ps" => "  د روغتیا لومړنیو پاملرنو ریاست PHC  ",
            ],

            "Directorate of Environmental Health" => [
                "fa" => "ریاست صحت محیطی ",
                "ps" => "د چاپیریال روغتیا ریاست",
            ],

            "Directorate of Infectious Disease Control" => [
                "fa" => " ریاست کنترول امراض ساری",
                "ps" => "د ساري ناروغیو د کنټرول ریاست",
            ],

            "Directorate of Mobile Health Services" => [
                "fa" => " ریاست مراقبت های صحی سیار",
                "ps" => "د ګرځنده روغتیايي خدمتونو ریاست",
            ],

            "Directorate of Public Nutrition" => [
                "fa" => "ریاست تغذی عامه ",
                "ps" => "د عامه تغذیې ریاست",
            ],

            "Directorate of Maternal, Newborn, and Child Health" => [
                "fa" => " ریاست صحت باروری مادر٬ نوزاد و طفل",
                "ps" => "د کوچنیانو، نویو زیږېدلو او بارورۍ روغتیا ریاست",
            ],

            "Directorate of Forensic Medicine" => [
                "fa" => "ریاست طب عدلی ",
                "ps" => "د عدلي طب ریاست",
            ],

            "Department of Emergency Management" => [
                "fa" => " آمریت رسیدگی به حوادث غیرمترقبه",
                "ps" => "ناڅاپي پېښو ته د رسېدنې آمریت",
            ],

            "Directorate of Private Sector Coordination" => [
                "fa" => "ریاست تنظیم هماهنگی سکتور خصوصی ",
                "ps" => "د خصوصي سکتور د همغږۍ او تنظیم ریاست",
            ],

            "General Directorate of the National Public Health Institute" => [
                "fa" => " ریاست عمومی انیستیتوت ملی صحت عامه ",
                "ps" => "د عامې روغتیا ملي انسټېټیوټ لوی ریاست",
            ],

            "Directorate of Public Health Education and Management" => [
                "fa" => "ریاست آموزش صحت عامه و مدیریت  ",
                "ps" => "د عامه روغتیايي زده کړو او مدیریت ریاست",
            ],

            "Directorate of Public Health Research and Clinical Studies" => [
                "fa" => " ریاست تحقیقات صحت عامه و مطالعات کلینیکی",
                "ps" => "د کلینیکي مطالعاتو او عامې روغتیا د څېړنو ریاست",
            ],

            "General Directorate of Policy and Planning" => [
                "fa" => " ریاست عمومی پالیسی و پلان",
                "ps" => "د پلان او پالیسۍ لوی ریاست",
            ],

            "Directorate of Planning and Strategic Planning" => [
                "fa" => " ریاست برنامه ریزی و پلانگذاری",
                "ps" => "د برنامه ریزۍ او پلانګزارۍ ریاست",
            ],

            "Directorate of Health Economics and Funding" => [
                "fa" => " ریاست اقتصاد و تمویل صحت ",
                "ps" => "د روغتیا د تمویل او اقتصاد ریاست",
            ],

            "Executive Directorate of the National Accreditation Authority for Health Facilities" => [
                "fa" => "ریاست اجرائیوی اداره ملی اعتبار دهی مراکز صحی  ",
                "ps" => "د روغتیايي مرکزونو د اعتبار ورکولو ملي ادارې اجرائیوي ریاست",
            ],

            "Directorate of Public-Private Partnership" => [
                "fa" => " ریاست مشارکت عامه و خصوصی",
                "ps" => "د خصوصي او عامه مشارکت ریاست",
            ],

            "Directorate of Protection of Children and Maternal Health Rights" => [
                "fa" => "ریاست حمایت از حقوق صحی اطفال و مادران ",
                "ps" => "د کوچنیانو او مېندو له روغتیايي حقوقو څخه د تمویل ریاست",
            ],

            "Directorate of Legal Affairs and Legislation" => [
                "fa" => "ریاست امور حقوقی و تقنین ",
                "ps" => "د تقنین او حقوقي چارو ریاست",
            ],

            "General Directorate of Pharmaceutical and Health Products Regulation" => [
                "fa" => " ریاست عمومی تنظیم ادویه و محصولات صحی ",
                "ps" => "د درملو او روغتیايي محصولاتو د ترتیب لوی ریاست",
            ],

            "Directorate of Licensing for Pharmaceutical Facilities and Activities" => [
                "fa" => " ریاست جوازدهی به تاسیسات و فعالیت های دوایی",
                "ps" => "تاسیساتو ته د جوازونو د ورکړې او درملیزو فعالیتونو ریاست",
            ],

            "Directorate of Drug and Health Product Evaluation and Registration" => [
                "fa" => "ریاست ارزیابی و ثبت ادویه و محصولات صحی ",
                "ps" => "د درملواو روغتیايي محصولاتو د ثبت او څېړنې ریاست",
            ],

            "Directorate of Pharmaceutical and Health Product Import and Export Regulation
            " => [
                "fa" => "ریاست تنطیم صادرات و واردات ادویه ومحصولات صحی ",
                "ps" => "د روغتیايي محصولاتو او درملو د صادرولو او وارداتو د تنظیم ریاست",
            ],

            "General Directorate of Food Safety" => [
                "fa" => "ریاست عمومی مصؤنیت غذایی ",
                "ps" => "د خوړو د ساتلو لوی ریاست",
            ],

            "Directorate of Food Licensing and Registration" => [
                "fa" => "ریاست جوازدهی و ثبت مواد غذایی ",
                "ps" => "د خوراکي توکو د ثبت او جوازونو ورکولو ریاست",
            ],

            "Directorate of Food Surveillance, Risk Analysis, and Standards Development" => [
                "fa" => " ریاست تحلیل خطر سرویلانس مواد غذایی وتدوین استندردها",
                "ps" => "د سرویلانس خطرونو او خوراکي توکو د څېړنو او د معیارونو پلي کولو ریاست",
            ],

            "Directorate of Document Analysis and Activity Regulation" => [
                "fa" => "ریاست تحلیل اسناد و تنظیم فعالیت ها ",
                "ps" => "د فعالیتونو د تنظیم او د اسنادو د څېړلو ریاست",
            ],

            "Directorate of Food, Drug, and Health Product Quality Control (Laboratory)" => [
                "fa" => " ریاست کنترول کیفیت غذا ٬ ادویه و محصولات صحی (لابراتوار)",
                "ps" => "د روغتیا لابراتواري محصولاتو،درملو او خوراکي توکو د کیفیت کنټرول ریاست ",
            ],

            "Directorate of Pharmaceutical Services" => [
                "fa" => "ریاست خدمات دوایی ",
                "ps" => "د درملي خدمتونو ریاست",
            ],

            "Directorate of Overseas Health Coordination Centers" => [
                "fa" => "ریاست هماهنگ کننده مراکز صحی خارج از کشور ",
                "ps" => "له هېواده بهر روغتیايي مرکزونو د همغږۍ ریاست",
            ],

            "Directorate of Overseas Health Affairs – Karachi" => [
                "fa" => " ریاست امور صحی خارج مرز کراچی",
                "ps" => "له هېواده بهر د کراچۍ د روغتیايي چارو ریاست",
            ],

            "Directorate of Overseas Health Affairs – Peshawar" => [
                "fa" => " ریاست امورصحی خارج مرز پشاور",
                "ps" => "له هېواده بهر پشاور د روغتیايي چارو ریاست",
            ],

            "Directorate of Overseas Health Affairs – Quetta" => [
                "fa" => "ریاست امورصحی خارج مرز کوته ",
                "ps" => "له هېواده بهر کوټه د روغتیايي چارو ریاست",
            ],

            "Directorate of Finance and Accounting" => [
                "fa" => "ریاست امور مالی و حسابی  ",
                "ps" => "د مالي او حسابي چارو ریاست",
            ],

            "Directorate of Procurement" => [
                "fa" => "ریاست تدارکات ",
                "ps" => "  د تدارکاتو ریاست  ",
            ],


            "Directorate of Administration" => [
                "fa" => "ریاست اداری",
                "ps" => "اداري ریاست",
            ],


            "General Directorate of Human Resources" => [
                "fa" => "ریاست عمومی منابع بشری ",
                "ps" => "د بشري سرچینو لوی ریاست",
            ],


            "Directorate of Capacity Building" => [
                "fa" => "ریاست ارتقای ظرفیت  ",
                "ps" => "د ظرفیت لوړلو ریاست",
            ],


            "Directorate of Prof. Ghazanfar Institute of Health Sciences" => [
                "fa" => "ریاست انیستیتوت علوم صحی پوهاند غضنفر ",
                "ps" => "د پوهاند غنضنفر روغتیايي علومو انسټېټیوټ ریاست",
            ],

            "Directorate of Private Health Sciences Institutes" => [
                "fa" => "ریاست انیستیتوت های علوم صحی خصوصی ",
                "ps" => "د خصوصي روغتیايي علومو انسټېټیوټونو ریاست",
            ],

            "General Directorate of Specialty" => [
                "fa" => " ریاست عمومی اکمال تخصص",
                "ps" => "د اکمال تخصص لوی ریاست",
            ],

            "Directorate of Operations" => [
                "fa" => "  ریاست عملیاتی",
                "ps" => "عملیاتي ریاست",
            ],

            "Directorate of Academic Coordination" => [
                "fa" => "  ریاست امور انسجام اکادمیک",
                "ps" => "د اکاډمیکو چارو د انسجام ریاست",

            ],
        ];
        foreach ($destination as $name => $destinations) {
            // Create the country record
            $des =  Destination::factory()->create([
                "color" => "#B4D455",
                "destination_type_id" => $directorate->id,
            ]);
            DestinationTrans::factory()->create([
                "value" => trim($name),
                "destination_id" => $des->id,
                "language_name" => "en",
            ]);
            // Loop through translations (e.g., fa, ps)
            foreach ($destinations as $key => $value) {
                DestinationTrans::factory()->create([
                    "value" => trim($value),
                    "destination_id" => $des->id,
                    "language_name" => $key,
                ]);
            }
        }
    }
}
