<?php

namespace App\Http\Middleware\api\template\user\sub;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserHasSubDeletePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission = null, $subPermission = null): Response
    {
        $authUser = $request->user();

        if ($authUser) {
            // 1. Check user has user permission
            $permission = DB::table("user_permissions as up")
                ->where("user_id", "=", $authUser->id)
                ->where("permission", $permission)
                ->join("user_permission_subs as ups", function ($join) use ($subPermission) {
                    return $join->on('ups.user_permission_id', '=', 'up.id')
                        ->where('ups.sub_permission_id', $subPermission)
                        ->where('ups.delete', true);
                })->select("ups.id")->first();

            if ($permission) {
                return $next($request);
            }
        }
        return response()->json([
            'message' => __('app_translation.unauthorized'),
        ], 403, [], JSON_UNESCAPED_UNICODE);
    }
}
