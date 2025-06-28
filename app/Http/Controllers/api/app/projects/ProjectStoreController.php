<?php

namespace App\Http\Controllers\api\app\projects;

use App\Models\Email;
use App\Models\Contact;
use App\Enums\LanguageEnum;
use Illuminate\Http\Request;
use App\Models\ProjectManager;
use App\Models\ProjectManagerTran;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\project\ProjectStoreRequest;

class ProjectStoreController extends Controller
{
    //


    public function create(ProjectStoreRequest $request)
    {
        $authUser = $request->user();
        $user_id = $authUser->id;

        $project_manager = null;

        // If no project_manager_id is provided, try to create a new one
        if (!$request->project_manager_id) {
            // 1. Check email
            $email = Email::where('value', $request->pro_manager_email)->first();
            if ($email) {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            } else {
                $email = Email::create(['value' => $request->pro_manager_email]);
            }

            // 2. Check contact
            $contact = Contact::where('value', $request->pro_manager_contact)->first();
            if ($contact) {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            } else {
                $contact = Contact::create(['value' => $request->pro_manager_contact]);
            }

            // 3. Create project manager
            $project_manager = ProjectManager::create([
                'email_id' => $email->id,
                'contact_id' => $contact->id,
                'ngo_id' => $user_id,
            ]);

            // 4. Create translations for all languages
            foreach (LanguageEnum::LANGUAGES as $code => $name) {
                $fieldName = 'pro_manager_name_' . $name;

                ProjectManagerTran::create([
                    'project_manager_id' => $project_manager->id,
                    'fullname' => $request->get($fieldName),
                    'language_name' => $code,
                ]);
            }
        } else {
            // If existing project_manager_id is provided
            $project_manager = $request->project_manager_id;
        }
    }
}
