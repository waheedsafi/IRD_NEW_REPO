<?php

namespace App\Http\Controllers\api\auth;

use App\Enums\RoleEnum;
use App\Enums\Type\StatusTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Email;
use App\Models\NgoStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NgoAuthController extends Controller
{
    public function authNgo(Request $request)
    {
        $ngo = $request->user();

        $authNgo =  DB::table('ngos as n')
            ->where('n.id', $ngo->id)
            ->leftjoin('emails as e', function ($join) {
                $join->on('n.email_id', '=', 'e.id');
            })
            ->leftjoin('ngo_statuses as ns', function ($join) {
                $join->on('ns.ngo_id', '=', 'n.id')
                    ->whereRaw('ns.created_at = (select max(ns2.created_at) from ngo_statuses as ns2 where ns2.ngo_id = n.id)');
            })
            ->leftjoin('roles as r', function ($join) {
                $join->on('n.role_id', '=', 'r.id');
            })
            ->select(
                "n.id",
                "n.profile",
                "n.username",
                "n.role_id",
                "n.email_id",
                "e.value as email",
                "n.is_editable",
                "n.created_at",
                "ns.status_type_id",
                "r.name as role_name"
            )->first();

        $ngoPermissions = $this->permission($authNgo);

        return response()->json(
            array_merge([
                "user" => [
                    "id" => $authNgo->id,
                    "profile" => $authNgo->profile,
                    "username" => $authNgo->username,
                    "email" => ['id' => $authNgo->email_id, 'value' => $authNgo->email],
                    "is_editable" => $authNgo->is_editable,
                    "created_at" => $authNgo->created_at,
                    "role" => ["role" => $authNgo->role_id, "name" => $authNgo->role_name],
                    "status_type_id" => $authNgo->status_type_id
                ]
            ], [
                "permissions" => $ngoPermissions,
            ]),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $email = Email::where('value', '=', $credentials['email'])->first();
        if (!$email) {
            return response()->json([
                'message' => __('app_translation.email_not_found'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
        $loggedIn = Auth::guard('ngo:api')->attempt([
            "email_id" => $email->id,
            "password" => $request->password,
        ]);
        if ($loggedIn) {
            // Get the auth user
            $ngo = $loggedIn['user'];
            $ngoStatus = NgoStatus::where("ngo_id", $ngo->id)->first();
            if ($ngoStatus->status_type_id == StatusTypeEnum::blocked->value) {
                return response()->json([
                    'message' => __('app_translation.account_is_block'),
                ], 403, [], JSON_UNESCAPED_UNICODE);
            }
            // Check If Ngo logged in for first time change to un_registered
            else if ($ngoStatus->status_type_id  == StatusTypeEnum::not_logged_in->value) {
                $ngoStatus->status_type_id = StatusTypeEnum::unregistered->value;
                $ngoStatus->save();
            }

            $authNgo =  DB::table('ngos as n')
                ->where('n.id', $ngo->id)
                ->leftjoin('ngo_statuses as ns', function ($join) {
                    $join->on('ns.ngo_id', '=', 'n.id')
                        ->whereRaw('ns.created_at = (select max(ns2.created_at) from ngo_statuses as ns2 where ns2.ngo_id = n.id)');
                })
                ->leftjoin('roles as r', function ($join) {
                    $join->on('n.role_id', '=', 'r.id');
                })
                ->select(
                    "n.id",
                    "n.profile",
                    "n.username",
                    "n.email_id",
                    "n.role_id",
                    "n.is_editable",
                    "n.created_at",
                    "ns.status_type_id",
                    "r.name as role_name"
                )->first();

            $ngoPermissions = $this->permission($authNgo);

            return response()->json(
                array_merge([
                    "user" => [
                        "id" => $authNgo->id,
                        "profile" => $authNgo->profile,
                        "username" => $authNgo->username,
                        "email" => ['id' => $authNgo->email_id, 'value' => $credentials['email']],
                        "is_editable" => $authNgo->is_editable,
                        "created_at" => $authNgo->created_at,
                        "role" => ["role" => $authNgo->role_id, "name" => $authNgo->role_name],
                        "status_type_id" => $authNgo->status_type_id
                    ]
                ], [
                    "token" => $loggedIn['tokens']['access_token'],
                    "permissions" => $ngoPermissions,
                ]),
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        } else {
            return response()->json([
                'message' => __('app_translation.ngo_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->invalidateToken(); // Calls the invalidateToken method defined in the trait
        return response()->json([
            'message' => __('app_translation.user_logged_out_success')
        ], 204, [], JSON_UNESCAPED_UNICODE);
    }
    // HELPER
    protected function permission($ngo)
    {
        $ngo_id = $ngo->id;
        $ngoPermissions = DB::table('ngos as n')
            ->where('n.id', $ngo_id)
            ->leftJoin('ngo_permissions as np', function ($join) {
                $join->on('np.ngo_id', '=', 'n.id');
            })
            ->join('permissions as p', function ($join) {
                $join->on('p.name', '=', 'np.permission');
            })
            ->select(
                "p.name as permission",
                "p.icon as icon",
                "p.priority as priority",
                "np.view",
                "np.add",
                "np.delete",
                "np.edit",
                "np.visible",
                "np.id",
            )
            ->orderBy("p.priority")
            ->get();

        return $ngoPermissions;
    }
}
