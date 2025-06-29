<?php

namespace App\Http\Controllers\api\app\projects;

use App\Models\Email;
use App\Models\Contact;
use App\Models\Project;
use App\Enums\LanguageEnum;
use App\Models\ProjectTran;
use App\Models\Representer;
use Illuminate\Http\Request;
use App\Models\ProjectDetail;
use App\Models\ProjectManager;
use App\Enums\Type\TaskTypeEnum;
use App\Models\ProjectDetailTran;
use App\Models\ProjectManagerTran;
use App\Models\ProjectRepresenter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectDistrictDetail;
use App\Models\ProjectDistrictDetailTran;
use App\Http\Requests\app\project\ProjectStoreRequest;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class ProjectStoreController extends Controller
{
    //

    protected $pendingTaskRepository;
    protected $storageRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        StorageRepositoryInterface $storageRepository

    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->storageRepository = $storageRepository;
    }


    public function store(ProjectStoreRequest $request)
    {
        $authUser = $request->user();
        $user_id = $authUser->id;

        DB::beginTransaction();
        $project_manager = null;

        // If no project_manager_id is provided, create new
        if (!$request->project_manager_id) {
            // 1. Email Check
            $email = Email::where('value', $request->pro_manager_email)->first();
            if ($email) {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            } else {
                $email = Email::create(['value' => $request->pro_manager_email]);
            }

            // 2. Contact Check
            $contact = Contact::where('value', $request->pro_manager_contact)->first();
            if ($contact) {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            } else {
                $contact = Contact::create(['value' => $request->pro_manager_contact]);
            }

            // 3. Create Project Manager
            $project_manager = ProjectManager::create([
                'email_id' => $email->id,
                'contact_id' => $contact->id,
                'ngo_id' => $user_id,
            ]);

            // 4. Add ProjectManager Translations
            foreach (LanguageEnum::LANGUAGES as $code => $lang) {
                $field = 'pro_manager_name_' . $lang;

                ProjectManagerTran::create([
                    'project_manager_id' => $project_manager->id,
                    'fullname' => $request->get($field),
                    'language_name' => $code,
                ]);
            }
        } else {
            // Use existing
            $project_manager = ProjectManager::findOrFail($request->project_manager_id);
        }

        // Get NGO representer
        $representer = Representer::where('ngo_id', $user_id)
            ->where('is_active', true)
            ->firstOrFail();

        // Create the main Project
        $project = Project::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'approved_date' => '0000-00-00',
            'total_budget' => $request->budget,
            'donor_registration_no' => $request->donor_register_no,
            'currency_id' => $request->currency['id'],
            'donor_id' => $request->donor['id'],
            'project_manager_id' => $project_manager->id,
            'country_id' => 2, // hardcoded — optional improvement: make dynamic
            'representer_id' => $representer->id,
        ]);
        ProjectRepresenter::create([
            'project_id' => $project->id,
            'representer_id' => $representer->id,
        ]);

        // Store Project Translations
        $translationFields = [
            'preamble'           => 'preamble',
            'health_experience'  => 'prev_proj_activi',
            'goals'              => 'project_goals',
            'objectives'         => 'project_object',
            'expected_outcome'   => 'expected_outcome',
            'expected_impact'    => 'expected_impact',
            'subject'   => 'subject', //
            'main_activities'    => 'main_activities',
            'introduction'       => 'project_intro',
            'operational_plan'   => 'operational_plan',
            'mission'   => 'mission', //
            'vission'   => 'vission', //
            'terminologies' => 'termin',
            'name' => 'project_name',
            'prev_proj_activi' => 'prev_proj_activi'
        ];

        foreach (LanguageEnum::LANGUAGES as $code => $lang) {
            $data = [
                'project_id'     => $project->id,
                'language_name'  => $code,
            ];

            foreach ($translationFields as $column => $inputPrefix) {
                $inputKey = "{$inputPrefix}_{$lang}";
                $data[$column] = $request->get($inputKey) ?? '';
            }

            ProjectTran::create($data);
        }


        foreach ($request->centers_list as $center) {
            $projectDetail = ProjectDetail::create([
                'project_id' => $project->id, // pass it externally
                'province_id' => $center['province']['id'],
                'budget' => $center['budget'],
                'direct_beneficiaries' => $center['direct_benefi'],
                'in_direct_beneficiaries' => $center['in_direct_benefi'],
            ]);

            foreach (LanguageEnum::LANGUAGES as $code => $lang) {
                ProjectDetailTran::create([
                    'project_detail_id' => $projectDetail->id,
                    'language_name' => $code,
                    'health_center' => json_encode(['name' => $center["health_centers_$lang"]]),
                    'address' => $center["address_$lang"],
                    'health_worker' => json_encode(['count' => $center["health_worker_$lang"]]),
                    'managment_worker' => json_encode(['count' => $center["fin_admin_employees_$lang"]]),
                ]);
            }

            foreach ($center['district'] as $district) {
                $districtDetail = ProjectDistrictDetail::create([
                    'project_detail_id' => $projectDetail->id,
                    'district_id' => $district['id'],
                ]);

                $villageData = $center['villages'][$district['id']] ?? [];

                foreach (LanguageEnum::LANGUAGES as $code => $lang) {


                    ProjectDistrictDetailTran::create([
                        'project_district_detail_id' => $districtDetail->id,
                        'language_name' => $code,
                        'villages' => json_encode([
                            ['name' => $villageData["village_$lang"] ?? '']
                        ]),
                    ]);
                }
            }
        }

        // // 3. Ensure task exists before proceeding
        // $task = $this->pendingTaskRepository->pendingTaskExist(
        //     $authUser,
        //     TaskTypeEnum::project_registeration,
        //     $user_id
        // );
        // if (!$task) {
        //     return response()->json([
        //         'message' => __('app_translation.task_not_found'),
        //     ], 404, [], JSON_UNESCAPED_UNICODE);
        // }

        DB::commit();

        return response()->json([
            'message' => 'Project created successfully.',
            'project_id' => $project->id,
        ], 201);
    }
}
