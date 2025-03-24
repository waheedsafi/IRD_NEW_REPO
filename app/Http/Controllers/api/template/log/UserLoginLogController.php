<?php

namespace App\Http\Controllers\api\template\log;

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class UserLoginLogController extends Controller
{
    //



    public function logs(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        // Start building the query
        $query = DB::table('user_login_logs as log')
            ->leftJoin(DB::raw('(SELECT id, username, "User" as user_type FROM users 
                             UNION ALL 
                             SELECT id, username, "Ngo" as user_type FROM ngos) as usr'), function ($join) {
                $join->on('log.userable_id', '=', 'usr.id')
                    ->whereRaw('log.userable_type = usr.user_type');
            })
            ->select(
                "log.id",
                "usr.username",
                "log.userable_type",
                "log.action",
                "log.ip_address",
                "log.browser",
                "log.device"
            );

        // Fetch results
        // return $query;
        // $logs = $query->get();

        $this->applyDate($query, $request);
        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            [
                "logs" => $tr,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    protected function applyDate($query, $request)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where('n.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('n.created_at', '<=', $endDate);
        }
    }
    // search function 
    protected function applySearch($query, $request)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        $allowedColumns = ['username', 'action', 'ip_address'];

        if ($searchColumn && $searchValue) {
            $allowedColumns = [
                'username' => 'usr.username',
                'action' => 'log.action',
                'ip_address' => 'log.ip_address'
            ];
            // Ensure that the search column is allowed
            if (in_array($searchColumn, array_keys($allowedColumns))) {
                $query->where($allowedColumns[$searchColumn], 'like', '%' . $searchValue . '%');
            }
        }
    }
    // filter function
    protected function applyFilters($query, $request)
    {

        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default 
        $allowedColumns = [
            'action' => 'log.action',
            'created_at' => 'log.created_at',
            'username' => 'usr.username',

        ];
        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
