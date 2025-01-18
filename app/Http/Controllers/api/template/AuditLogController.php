<?php

namespace App\Http\Controllers\api\template;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Audit;
use Illuminate\Support\Facades\App;

class AuditLogController extends Controller
{

    public function audits(Request $request, $page)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        // Start building the query
        $query = [];

        $query = Audit::all()
            ->with('user'); // Eager load the user who performed the action

        // if ($locale === LanguageEnum::default->value) {
        //     $query = UsersEnView::query();
        // } else if ($locale === LanguageEnum::farsi->value) {
        //     $query = UsersFaView::query();
        // } else {
        //     $query = UsersPsView::query();
        // }
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate || $endDate) {
            // Apply date range filtering
            if ($startDate && $endDate) {
                $query->whereBetween('createdAt', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->where('createdAt', '>=', $startDate);
            } elseif ($endDate) {
                $query->where('createdAt', '<=', $endDate);
            }
        }

        // Apply search filter if present
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = ['username', 'contact', 'email'];

            // Ensure that the search column is allowed
            if (in_array($searchColumn, $allowedColumns)) {
                $query->where($searchColumn, 'like', '%' . $searchValue . '%');
            }
        }

        // Apply sorting if present
        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default is 'asc')

        // Apply sorting by provided column or default to 'created_at'
        if ($sort && in_array($sort, ['username', 'createdAt', 'status', 'job', 'destination'])) {
            $query->orderBy($sort, $order);
        } else {
            // Default sorting if no sort is provided
            $query->orderBy("createdAt", $order);
        }

        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            [
                "users" => $tr,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
