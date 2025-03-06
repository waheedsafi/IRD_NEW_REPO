<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ngo;
use App\Models\News;
use App\Models\Role;
use App\Models\User;

use App\Models\Email;
use App\Models\Staff;
use App\Models\Gender;

use App\Enums\RoleEnum;
use App\Models\Address;
use App\Models\Country;
use App\Models\NgoTran;
use App\Enums\StaffEnum;
use App\Models\Director;
use App\Models\District;
use App\Models\Document;
use App\Models\Province;
use App\Models\Agreement;
use App\Models\CheckList;
use App\Models\Translate;
use App\Enums\LanguageEnum;
use App\Models\AddressTran;
use App\Models\PendingTask;
use App\Models\NidTypeTrans;
use Illuminate\Http\Request;
use App\Enums\PermissionEnum;
use App\Models\CheckListType;
use Sway\Models\RefreshToken;
use App\Models\RolePermission;
use App\Models\StatusTypeTran;
use App\Models\UserPermission;
use App\Enums\CheckListTypeEnum;
use App\Enums\SubPermissionEnum;
use App\Models\RolePermissionSub;
use App\Enums\DestinationTypeEnum;
use App\Enums\Type\StatusTypeEnum;
use App\Models\PendingTaskContent;
use Illuminate\Support\Facades\DB;
use App\Models\PendingTaskDocument;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Traits\Address\AddressTrait;

use function Laravel\Prompts\select;
use App\Enums\CheckList\CheckListEnum;
use App\Enums\Type\RepresenterTypeEnum;
use App\Enums\Type\RepresentorTypeEnum;
use App\Repositories\ngo\NgoRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

class TestController extends Controller
{
    protected $ngoRepository;
    protected $userRepository;

    public function __construct(
        NgoRepositoryInterface $ngoRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->ngoRepository = $ngoRepository;
        $this->userRepository = $userRepository;
    }
    use AddressTrait;
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $ngo_id = 8;
        $ids = [
            CheckListEnum::ngo_register_form_en->value,
            CheckListEnum::ngo_register_form_fa->value,
            CheckListEnum::ngo_register_form_ps->value
        ];
        $languageCodes = [
            CheckListEnum::ngo_register_form_en->value => "en",
            CheckListEnum::ngo_register_form_fa->value => "fa",
            CheckListEnum::ngo_register_form_ps->value => "ps"
        ];

        // Initialize an array to hold the results
        $result = [];

        // Iterate over the checklist_ids and check if they exist in the table
        foreach ($ids as $id) {
            // Check if the checklist_id exists in the table
            $exists = DB::table('agreements as a')
                ->where('a.ngo_id', $ngo_id)
                ->where("a.start_date", null)
                ->join('agreement_documents as ad', "ad.agreement_id", '=', "a.id")
                ->join('documents as d', function ($join) use ($id) {
                    $join->on('d.id', "=", "ad.document_id")
                        ->where('d.check_list_id', $id);
                })
                ->exists();

            // If the ID doesn't exist, return the corresponding language code
            if (!$exists) {
                $result[] = $languageCodes[$id];
            }
        }

        // Return the result, which will contain missing language codes
        return response()->json($result);
    }
}
