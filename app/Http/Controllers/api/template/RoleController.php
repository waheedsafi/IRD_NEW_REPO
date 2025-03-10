<?php

namespace App\Http\Controllers\api\template;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function roles()
    {
        $excludedIds = [
            RoleEnum::super->value,
            RoleEnum::debugger->value,
            RoleEnum::donor->value,
            RoleEnum::ngo->value,
        ];
        return response()->json(Role::whereNotIn('id', $excludedIds)->select("name", 'id', 'created_at as createdAt')->get());
    }
}
