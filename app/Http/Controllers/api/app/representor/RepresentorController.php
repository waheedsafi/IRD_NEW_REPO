<?php

namespace App\Http\Controllers\api\app\representor;

use App\Models\Agreement;
use App\Enums\LanguageEnum;
use App\Models\Representer;
use App\Models\RepresenterTran;
use App\Models\AgreementDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Traits\File\PendingFileTrait;
use App\Enums\CheckList\CheckListEnum;
use App\Http\Requests\app\representor\StoreRepresentorRequest;
use App\Http\Requests\app\representor\UpdateRepresentorRequest;

class RepresentorController extends Controller
{
    use PendingFileTrait;
    public function ngoRepresentor($id)
    {
        $representor = DB::table('representers as r')
            ->where('r.id', $id)
            ->join('documents as d', 'd.id', 'r.document_id')
            ->join('check_lists as cl', 'cl.id', 'd.check_list_id')
            ->joinSub(function ($query) {
                $query->from('representer_trans as rt')
                    ->select(
                        'representer_id',
                        DB::raw("MAX(CASE WHEN language_name = 'fa' THEN full_name END) as repre_name_farsi"),
                        DB::raw("MAX(CASE WHEN language_name = 'en' THEN full_name END) as repre_name_english"),
                        DB::raw("MAX(CASE WHEN language_name = 'ps' THEN full_name END) as repre_name_pashto")
                    )
                    ->groupBy('representer_id');
            }, 'rt', 'rt.representer_id', '=', 'r.id')
            ->select(
                'r.id',
                'r.is_active',
                'rt.repre_name_farsi',
                'rt.repre_name_english',
                'rt.repre_name_pashto',
                'd.id as document_id',
                'd.actual_name',
                'd.path',
                'd.type',
                'd.size',
                'd.check_list_id',
                'cl.id as check_list_id',
                'cl.acceptable_mimes',
                'cl.acceptable_extensions',
                'cl.file_size'
            )
            ->first();

        $result =  [
            'id' => $representor->id,
            'is_active' => (bool) $representor->is_active,
            'repre_name_farsi' => $representor->repre_name_farsi,
            'repre_name_english' => $representor->repre_name_english,
            'repre_name_pashto' => $representor->repre_name_pashto,
            'letter_of_intro' => [
                "path" => $representor->path,
                "document_id" => $representor->document_id,
                "size" => $representor->size,
                "type" => $representor->type,
                "name" => $representor->actual_name,
                "checklist_id" => $representor->check_list_id,
            ],
            'checklist' => [
                "id" => $representor->check_list_id,
                "acceptable_mimes" => $representor->acceptable_mimes,
                "acceptable_extensions" => $representor->acceptable_extensions,
                "file_size" => $representor->file_size,
            ],
        ];


        return response()->json(
            $result,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function ngoRepresentors($ngo_id)
    {
        $locale = App::getLocale();
        $representor = DB::table('representers as r')
            ->where('r.ngo_id', $ngo_id)
            ->leftJoin('agreements as a', function ($join) {
                $join->on('a.representer_id', '=', 'r.id');
            })
            ->join('representer_trans as rt', function ($join) use ($locale) {
                $join->on('r.id', '=', 'rt.representer_id')
                    ->where('rt.language_name', $locale);
            })
            ->join('users as u', 'r.user_id', '=', 'u.id')
            ->select(
                'r.id',
                'r.is_active',
                'r.created_at',
                'rt.full_name',
                'u.username',
                'a.id as agreement_id',
                'a.agreement_no',
                'a.start_date',
                'a.end_date',
                "u.username as saved_by"
            )
            ->orderBy('r.id', 'desc')
            ->get();

        return response()->json(
            $representor,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function store(StoreRepresentorRequest $request)
    {
        $request->validated();
        $ngo_id = $request->ngo_id;
        $authUser = $request->user();
        // 1. Get current agreement
        $agreement = Agreement::where('ngo_id', $ngo_id)
            ->where('end_date', null)
            ->first();
        if (!$agreement) {
            return response()->json([
                'message' => __('app_translation.representor_add_error')
            ], 409);
        }
        // 2. Transaction
        DB::beginTransaction();
        // 3. Store document
        $result = $this->singleChecklistDBDocStore(
            $request->letter_of_intro['pending_id'],
            $agreement->id,
            $ngo_id
        );
        if ($result['success'] == false) {
            return $result['error'];
        }
        // To solve Multiple same checklist when new representor added.
        $id = DB::table('agreement_documents as ad')
            ->where('ad.agreement_id', $agreement->id)
            ->join('documents as d', function ($join) {
                $join->on('ad.document_id', '=', 'd.id')
                    ->where('d.check_list_id', CheckListEnum::ngo_representor_letter->value);
            })
            ->select('ad.id')
            ->first();
        AgreementDocument::where('id', $id->id)->update(['document_id' => $result['document']->id]);

        // 4. Update prevous representors status
        Representer::where('ngo_id', $ngo_id)->update(['is_active' => false]);
        // 5. Store representor
        $representer = Representer::create([
            'ngo_id' => $ngo_id,
            'user_id' => $authUser->id,
            'is_active' => true,
            "document_id" => $result['document']->id
        ]);
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            RepresenterTran::create([
                'representer_id' => $representer->id,
                'language_name' =>  $code,
                'full_name' => $request["repre_name_{$name}"],
            ]);
        }
        $agreement->representer_id = $representer->id;
        $agreement->save();
        DB::commit();
        $full_name = $request["repre_name_english"];
        $locale = App::getLocale();
        if ($locale == "fa") {
            $full_name = $request["repre_name_farsi"];
        } else if ($locale == "ps") {
            $full_name = $request["repre_name_pashto"];
        }
        return response()->json([
            "representor" => [
                "id" => $representer->id,
                "full_name" => $full_name,
                "is_active" => 1,
                "saved_by" => $authUser->username,
                "agreement_no" => $agreement->agreement_no,
                "agreement_id" => $agreement->id,
                "start_date" => $agreement->start_date,
                "end_date" => $agreement->end_date,
            ],
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(UpdateRepresentorRequest $request)
    {
        $request->validated();
        $representer_id = $request->id;
        $ngo_id = $request->ngo_id;
        $authUser = $request->user();
        // 1. Get current agreement
        $agreement = Agreement::where('ngo_id', $ngo_id)
            ->where('end_date', null)
            ->first();
        if (!$agreement) {
            return response()->json([
                'message' => __('app_translation.representor_add_error')
            ], 409);
        }
        $representer = Representer::find($representer_id);
        if (!$representer) {
            return response()->json([
                'message' => __('app_translation.representor_not_found')
            ], 404);
        }
        // 1. Transaction
        DB::beginTransaction();
        // 2. Store document
        if (isset($request->letter_of_intro['pending_id'])) {
            // 3. New document is added
            $result =  $this->singleChecklistDBDocStore(
                $request->letter_of_intro['pending_id'],
                $agreement->id,
                $ngo_id
            );
            if ($result['success'] == false) {
                return $result['error'];
            }
            $representer->document_id = $result['document']->id;
            if ($request->is_active) {
                $this->UpdateRepresenterChecklist($agreement->id, $result['document']->id);
                Representer::where('ngo_id', $ngo_id)->update(['is_active' => false]);
                $representer->is_active = true;
                $agreement->representer_id = $representer->id;
            } else {
            }
            $agreement->save();
        } else {
            if ($request->is_active) {
                // 3. Get Current Representer
                $this->UpdateRepresenterChecklist($agreement->id, $representer->document_id);
                Representer::where('ngo_id', $ngo_id)->update(['is_active' => false]);
                $representer->is_active = true;
                $agreement->representer_id = $representer->id;
            }
        }
        $trans = RepresenterTran::where('representer_id', $representer->id)
            ->select('id', 'language_name', 'full_name')
            ->get();
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran = $trans->where('language_name', $code)->first();
            if ($tran) {
                $tran->full_name = $request["repre_name_{$name}"];
                $tran->save();
            }
        }
        $agreement->save();
        $representer->user_id = $authUser->id;
        $representer->save();
        DB::commit();
        $full_name = $request["repre_name_english"];
        $locale = App::getLocale();
        if ($locale == "fa") {
            $full_name = $request["repre_name_farsi"];
        } else if ($locale == "ps") {
            $full_name = $request["repre_name_pashto"];
        }
        return response()->json([
            "representor" => [
                "id" => $representer->id,
                "full_name" => $full_name,
                "agreement_no" => $agreement->agreement_no,
                "is_active" => (bool) $representer->is_active,
                "saved_by" => $authUser->username,
                "start_date" => $agreement->start_date,
                "end_date" => $agreement->end_date,
            ],
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    protected function UpdateRepresenterChecklist($agreement_id, $document_id)
    {
        $id = DB::table('agreement_documents as ad')
            ->where('ad.agreement_id', $agreement_id)
            ->join('documents as d', function ($join) {
                $join->on('ad.document_id', '=', 'd.id')
                    ->where('d.check_list_id', CheckListEnum::ngo_representor_letter->value);
            })
            ->select('ad.id')
            ->first();
        AgreementDocument::where('id', $id->id)->update(['document_id' => $document_id]);
    }
}
