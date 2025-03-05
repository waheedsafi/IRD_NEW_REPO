<?php

namespace App\Http\Controllers\api\app\agreement;

use App\Enums\CheckList\CheckListEnum;
use App\Models\Agreement;
use App\Models\CheckList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Repositories\ngo\NgoRepositoryInterface;
use App\Models\Document;

class AgreementController extends Controller
{
    protected $ngoRepository;

    public function __construct(
        NgoRepositoryInterface $ngoRepository
    ) {
        $this->ngoRepository = $ngoRepository;
    }
    public function agreementDocuments(Request $request)
    {
        $ngo_id = $request->input('ngo_id');
        $agreement_id = $request->input('agreement_id');

        $locale = App::getLocale();
        $query = $this->ngoRepository->ngo($ngo_id);
        $documents = $this->ngoRepository->agreementDocuments($query, $agreement_id, $locale);

        return response()->json([
            'agreement_documents' => $documents,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function agreement(Request $request, $id)
    {
        $data = Agreement::select('id', 'start_date', 'end_date')->where('ngo_id', $id)->get();
        return response()->json([
            'message' => __('app_translation.success'),
            'agreement' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function agreementDocument(Request $request, $id)
    {

        $locale = App::getLocale();
        $tr = CheckList::join('check_list_trans as ct', 'ct.check_list_id', '=', 'check_lists.id')
            ->join('documents as doc', 'doc.check_list_id', 'check_lists.id')
            ->join('agreement_documents as agr', 'agr.document_id', 'doc.id')
            ->where('ct.language_name', $locale)
            ->where('agr.agreement_id', $id)
            ->select('check_lists.id as check_list_id', 'ct.value as check_list_name',  'doc.id as document_id', 'doc.type', 'check_lists.description', 'doc.path', 'doc.actual_name')
            ->orderBy('check_lists.id')
            ->get();

        return response()->json([
            'document' => $tr
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function registrationNotUploadList(Request $request)
    {
        // Fetch the agreement_id for the given ngo_id
        $agreement = Agreement::select('id')
            ->where('ngo_id', $request->ngo_id)
            ->where('start_data', '')
            ->where('end_date', '')
            ->first();

        if (!$agreement) {
            return response()->json(['error' => 'Agreement not found'], 404);
        }

        // Check the existence of documents for the given checklist IDs (en, fa, ps) in one query
        $checkListIds = [
            CheckListEnum::ngo_register_form_en,
            CheckListEnum::ngo_register_form_fa,
            CheckListEnum::ngo_register_form_ps,
        ];

        $documents = Document::join('agreement_documents', 'documents.id', '=', 'agreement_documents.document_id')
            ->where('agreement_documents.agreement_id', $agreement->id)
            ->whereIn('documents.check_list_id', $checkListIds)
            ->pluck('documents.check_list_id'); // Get only the check_list_id

        // Check for the existence of each document
        $registrationStatus = [
            'registration_en' => in_array(CheckListEnum::ngo_register_form_en, $documents),
            'registration_fa' => in_array(CheckListEnum::ngo_register_form_fa, $documents),
            'registration_ps' => in_array(CheckListEnum::ngo_register_form_ps, $documents),
        ];

        return $registrationStatus;
    }
}
