<?php

namespace App\Http\Controllers\api\app\projects;

use App\Models\Project;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use App\Models\ProjectDetail;
use App\Models\ProjectDetailTran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\ProjectDistrictDetail;
use App\Models\ProjectDistrictDetailTran;

class ProjectEditController extends Controller
{
    //

    public function details($id)
    {
        $map = [
            'preamble'           => 'preamble',
            'health_experience'  => 'exper_in_health',
            'goals'              => 'goals',
            'objectives'         => 'objective',
            'expected_outcome'   => 'expected_outcome',
            'expected_impact'    => 'expected_impact',
            'subject'            => 'subject',
            'main_activities'    => 'main_activities',
            'introduction'       => 'project_intro',
            'operational_plan'   => 'action_plan',
            'mission'            => 'mission',
            'vission'            => 'vission',
            'terminologies'      => 'abbreviat',
            'name'               => 'project_name',
            'prev_proj_activi'   => 'exper_in_health',
            'project_structure'  => 'project_structure',
        ];

        // Fetch project translations in ONE query
        $translations = DB::table('project_trans')
            ->where('project_id', $id)
            ->whereIn('language_name', [
                LanguageEnum::default->value,
                LanguageEnum::farsi->value,
                LanguageEnum::pashto->value,
            ])
            ->get()
            ->keyBy('language_name');

        // Build the result dynamically
        $result = [];
        foreach ($map as $dbColumn => $aliasBase) {
            $result["{$aliasBase}_english"] = $translations[LanguageEnum::default->value]->{$dbColumn} ?? null;
            $result["{$aliasBase}_farsi"]   = $translations[LanguageEnum::farsi->value]->{$dbColumn} ?? null;
            $result["{$aliasBase}_pashto"]  = $translations[LanguageEnum::pashto->value]->{$dbColumn} ?? null;
        }

        return response()->json(
            $result,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function budget($id)
    {
        $languages = LanguageEnum::LANGUAGES;
        $locale = App::getLocale();

        $project = DB::table('projects as pro')->where('pro.id', $id)
            ->join('donor_trans as don', function ($join) use ($locale) {
                $join->on('pro.donor_id', 'don.donor_id')
                    ->where('don.language_name', $locale);
            })
            ->join('currency_trans as cur', function ($join) use ($locale) {
                $join->on('pro.currency_id', 'cur.currency_id')
                    ->where('cur.language_name', $locale);
            })
            ->select(
                'pro.start_date',
                'pro.end_date',
                'pro.total_budget as budget',
                'pro.donor_registration_no',
                'don.donor_id',
                'don.name',
                'pro.currency_id',
                'cur.name as currency_name',
                'pro.approved_date'

            )
            ->first();
        // Fetch centers with province in current locale
        $centers = DB::table('project_details as pd')
            ->where('pd.project_id', $id)
            ->leftJoin('province_trans as p', function ($join) use ($locale) {
                $join->on('pd.province_id', '=', 'p.province_id')
                    ->where('p.language_name', $locale);
            })
            ->select(
                'pd.id',
                'pd.budget',
                'pd.direct_beneficiaries',
                'pd.in_direct_beneficiaries',
                'p.province_id as province_id',
                'p.value as province_name'
            )
            ->get()
            ->keyBy('id');

        // Center translations
        $centerTrans = DB::table('project_detail_trans')
            ->whereIn('project_detail_id', $centers->keys())
            ->get()
            ->groupBy('project_detail_id');

        // Districts only in app language
        $districts = DB::table('project_district_details as pdd')
            ->join('district_trans as dt', function ($join) use ($locale) {
                $join->on('pdd.district_id', '=', 'dt.district_id')
                    ->where('dt.language_name', $locale);
            })
            ->whereIn('pdd.project_detail_id', $centers->keys())
            ->select('pdd.id', 'pdd.project_detail_id', 'pdd.district_id', 'dt.value as district_name')
            ->get()
            ->groupBy('project_detail_id');

        // Villages in all languages
        $districtTrans = DB::table('project_district_detail_trans')
            ->whereIn(
                'project_district_detail_id',
                $districts->flatten()->pluck('id')
            )
            ->get()
            ->groupBy('project_district_detail_id');

        $result = [];

        foreach ($centers as $centerId => $center) {
            $item = [
                'id' => rand(1, 9999999999),
                'province' => [
                    'id'   => $center->province_id,
                    'name' => $center->province_name,
                ],
                'budget' => $center->budget,
                'direct_benefi' => $center->direct_beneficiaries,
                'in_direct_benefi' => $center->in_direct_beneficiaries,
            ];

            // Add center translations
            foreach ($languages as $code => $lang) {
                $tran = $centerTrans[$centerId]->firstWhere('language_name', $code);
                $item["health_centers_$lang"] = json_decode($tran->health_center ?? '[]', true);
                $item["address_$lang"] = $tran->address ?? null;
                $item["health_worker_$lang"] = json_decode($tran->health_worker ?? '[]', true);
                $item["fin_admin_employees_$lang"] = json_decode($tran->managment_worker ?? '[]', true);
            }

            // Add unique districts
            $item['district'] = [];
            $item['villages'] = [];

            foreach ($districts[$centerId] ?? [] as $district) {
                // Add district (only once)
                if (!collect($item['district'])->contains('id', $district->district_id)) {
                    $item['district'][] = [
                        'id' => $district->district_id,
                        'name' => $district->district_name, // only app language
                    ];
                }

                // Add villages in all languages for this district
                $villageData = ['district_id' => $district->district_id];
                foreach ($languages as $code => $lang) {
                    $tran = $districtTrans[$district->id]->firstWhere('language_name', $code);
                    $villageData["village_$lang"] = json_decode($tran->villages ?? '[]', true);
                }
                $item['villages'][] = $villageData;
            }

            $result[] = $item;
        }

        return $result = [
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'donor' => ['id' => $project->donor_id, 'name' => $project->name, 'created_at' => ''],
            'donor_register_no' => $project->donor_registration_no,
            'currency' => ['id' => $project->currency_id, 'name' => $project->currency_name, 'created_at' => ''],
            'budget' => $project->budget,
            'centers_list' => $result,
            'optional_lang' => $locale

        ];
        return response()->json(
            $result,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
