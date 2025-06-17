<?php

namespace App\Http\Controllers\api\auth;

use Exception;
use App\Models\User;
use App\Models\Email;
use Sway\Utils\StringUtils;
use Illuminate\Http\Request;
use App\Enums\Status\StatusEnum;
use App\Traits\Helper\HelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Requests\template\user\UpdateProfilePasswordRequest;
use App\Models\UserStatus;

class UserAuthController extends Controller
{
    use HelperTrait;
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function user(Request $request)
    {
        $locale = App::getLocale();
        $user = $request->user();

        $user = DB::table('users as u')
            ->where('u.id', $user->id)
            ->join('model_job_trans as mjt', function ($join) use ($locale) {
                $join->on('mjt.model_job_id', '=', 'u.job_id')
                    ->where('mjt.language_name', $locale);
            })
            ->leftJoin('contacts as c', 'c.id', '=', 'u.contact_id')
            ->join('emails as e', 'e.id', '=', 'u.email_id')
            ->join('roles as r', 'r.id', '=', 'u.role_id')
            ->select(
                'u.id',
                "u.profile",
                "u.grant_permission",
                'u.full_name',
                'u.username',
                'c.value as contact',
                'u.contact_id',
                'e.value as email',
                'r.name as role_name',
                'u.role_id',
                "mjt.value as job",
                "u.created_at"
            )
            ->first();

        return response()->json(
            [
                "user" => [
                    "id" => $user->id,
                    "full_name" => $user->full_name,
                    "username" => $user->username,
                    'email' => $user->email,
                    "profile" => $user->profile,
                    "grant" => (bool) $user->grant_permission,
                    "role" => ["role" => $user->role_id, "name" => $user->role_name],
                    'contact' => $user->contact,
                    "job" => $user->job,
                    "created_at" => $user->created_at,
                ],
                "permissions" => $this->userRepository->userAuthFormattedPermissions($user->id),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $locale = App::getLocale();
        $email = Email::where('value', '=', $credentials['email'])->first();
        if (!$email) {
            return response()->json([
                'message' => __('app_translation.email_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $loggedIn = Auth::guard('user:api')->attempt([
            "email_id" => $email->id,
            "password" => $request->password,
        ]);
        if ($loggedIn) {
            // Get the auth user
            $user = $loggedIn['user'];
            $userStatus = UserStatus::where("user_id", $user->id)
                ->where('is_active', true)
                ->first();
            if ($userStatus->status_id == StatusEnum::block->value) {
                return response()->json([
                    'message' => __('app_translation.account_is_block'),
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $user = DB::table('users as u')
                ->where('u.id', $user->id)
                ->join('model_job_trans as mjt', function ($join) use ($locale) {
                    $join->on('mjt.model_job_id', '=', 'u.job_id')
                        ->where('mjt.language_name', $locale);
                })
                ->leftJoin('contacts as c', 'c.id', '=', 'u.contact_id')
                ->join('roles as r', 'r.id', '=', 'u.role_id')
                ->select(
                    'u.id',
                    "u.profile",
                    "u.grant_permission",
                    'u.full_name',
                    'u.username',
                    'c.value as contact',
                    'u.contact_id',
                    'r.name as role_name',
                    'u.role_id',
                    "mjt.value as job",
                    "u.created_at",
                )
                ->first();

            $this->storeUserLog($request, $user->id, StringUtils::getModelName(User::class), "Login");
            return response()->json(
                [
                    "token" => $loggedIn['tokens']['access_token'],
                    "permissions" => $this->userRepository->userAuthFormattedPermissions($user->id),
                    "user" => [
                        "id" => $user->id,
                        "full_name" => $user->full_name,
                        "username" => $user->username,
                        'email' => $credentials['email'],
                        "profile" => $user->profile,
                        "grant" => (bool) $user->grant_permission,
                        "role" => ["role" => $user->role_id, "name" => $user->role_name],
                        'contact' => $user->contact,
                        "job" => $user->job,
                        "created_at" => $user->created_at,
                    ],
                ],
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        } else {
            return response()->json([
                'message' => __('app_translation.user_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function logout(Request $request)
    {
        $this->storeUserLog($request, $request->user()->id, StringUtils::getModelName(User::class), "Logout");

        $request->user()->invalidateToken(); // Calls the invalidateToken method defined in the trait
        return response()->json([
            'message' => __('app_translation.user_logged_out_success')
        ], 204, [], JSON_UNESCAPED_UNICODE);
    }
    public function changePassword(UpdateProfilePasswordRequest $request)
    {
        $request->validated();
        $authUser = $request->user();
        DB::beginTransaction();
        $request->validate([
            "old_password" => ["required", "min:8", "max:45"],
        ]);
        if (!Hash::check($request->old_password, $authUser->password)) {
            return response()->json([
                'errors' => ['old_password' => [__('app_translation.incorrect_password')]],
            ], 422, [], JSON_UNESCAPED_UNICODE);
        } else {
            $authUser->password = Hash::make($request->new_password);
            $authUser->save();
        }
        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
