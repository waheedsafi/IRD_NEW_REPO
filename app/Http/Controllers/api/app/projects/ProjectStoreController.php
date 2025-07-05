<?php

namespace App\Http\Controllers\api\app\projects;

use App\Enums\CountryEnum;
use App\Models\Email;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Document;
use App\Enums\LanguageEnum;
use App\Models\ProjectTran;
use App\Models\Representer;
use App\Models\ProjectDetail;
use App\Models\ProjectManager;
use App\Enums\Type\TaskTypeEnum;
use App\Models\ProjectDetailTran;
use App\Models\ProjectManagerTran;
use App\Models\ProjectRepresenter;
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectDistrictDetail;
use App\Models\ProjectDistrictDetailTran;
use App\Http\Requests\app\project\ProjectStoreRequest;
use App\Models\CurrencyTran;
use App\Models\ProjectDocument;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\Approval\ApprovalRepositoryInterface;
use App\Repositories\Director\DirectorRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Representative\RepresentativeRepositoryInterface;
use Illuminate\Support\Facades\App;

class ProjectStoreController extends Controller
{
    //

    use HelperTrait;
    protected $pendingTaskRepository;
    protected $notificationRepository;
    protected $approvalRepository;
    protected $directorRepository;
    protected $representativeRepository;
    protected $storageRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        NotificationRepositoryInterface $notificationRepository,
        ApprovalRepositoryInterface $approvalRepository,
        DirectorRepositoryInterface $directorRepository,
        RepresentativeRepositoryInterface $representativeRepository,
        StorageRepositoryInterface $storageRepository
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->notificationRepository = $notificationRepository;
        $this->approvalRepository = $approvalRepository;
        $this->directorRepository = $directorRepository;
        $this->representativeRepository = $representativeRepository;
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
            $request->validate([
                'pro_manager_name_english' => 'required|string',
                'pro_manager_name_farsi'   => 'required|string',
                'pro_manager_name_pashto'  => 'required|string',
                'pro_manager_contact'      => 'required|string|max:20',
                'pro_manager_email'        => 'required|email',
            ]);
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
                    'full_name' => $request->get($field),
                    'language_name' => $code,
                ]);
            }
        } else {
            // Use existing
            $project_manager = ProjectManager::findOrFail($request->project_manager_id);
        }


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
            'country_id' => CountryEnum::afghanistan->value, // hardcoded — optional improvement: make dynamic
            'registration_no' => '',
            'ngo_id' => $user_id,

        ]);
        $project->registration_no = 'IRD-P-' . $project->id;
        $project->save();

        // Store Project Translations
        $translationFields = [
            'preamble'           => 'preamble',
            'health_experience'  => 'exper_in_health',
            'goals'              => 'goals',
            'objectives'         => 'objective',
            'expected_outcome'   => 'expected_outcome',
            'expected_impact'    => 'expected_impact',
            'subject'   => 'subject', //
            'main_activities'    => 'main_activities',
            'introduction'       => 'project_intro',
            'operational_plan'   => 'action_plan',
            'mission'   => 'mission', //
            'vission'   => 'vission', //
            'terminologies' => 'abbreviat',
            'name' => 'project_name',
            'prev_proj_activi' => 'exper_in_health'
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
                    'health_center' => json_encode($center["health_centers_$lang"]),
                    'address' => $center["address_$lang"],
                    'health_worker' => json_encode($center["health_worker_$lang"]),
                    'managment_worker' => json_encode($center["fin_admin_employees_$lang"]),
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
                        'villages' => json_encode(
                            $villageData["village_$lang" ?? '']
                        ),
                    ]);
                }
            }
        }

        $task = $this->pendingTaskRepository->pendingTaskExist(
            $request->user(),
            TaskTypeEnum::project_registeration->value,
            $user_id
        );
        if (!$task) {
            return response()->json([
                'message' => __('app_translation.task_not_found')
            ], 404);
        }
        $documentsId = [];
        $this->storageRepository->projectDocumentStore($project->id, $user_id, $task->id, function ($documentData) use (&$documentsId) {
            $checklist_id = $documentData['check_list_id'];
            $document = Document::create([
                'actual_name' => $documentData['actual_name'],
                'size' => $documentData['size'],
                'path' => $documentData['path'],
                'type' => $documentData['type'],
                'check_list_id' => $checklist_id,
            ]);
            array_push($documentsId, $document->id);
            ProjectDocument::create([
                'document_id' => $document->id,
                'project_id' => $documentData['project_id'],
            ]);
        });

        DB::commit();
        $this->pendingTaskRepository->destroyPendingTask(
            $request->user(),
            TaskTypeEnum::project_registeration,
            $user_id
        );
        $project_name = $request->project_name_english;
        $locale = App::getLocale();
        if ($locale === 'fa') {
            $project_name = $request->project_name_farsi;
        }
        if ($locale === 'ps') {
            $project_name = $request->project_name_pashto;
        }
        $data = [
            'id' => $project->id,
            'budget' => $request->budget,
            'start_date' => $request->start_date,
            'currency' => $request->currency->name,
            'end_date' => $request->end_date,
            'donor_registration_no' => $request->donor_register_no,
            'project_name' => $project_name,
            'donor' => $request->donor->name,
            'created_at' => $project->created_at,
        ];
        return response()->json([
            'message' => 'Project created successfully.',
            'project' => $data,
        ], 200);
    }
}
