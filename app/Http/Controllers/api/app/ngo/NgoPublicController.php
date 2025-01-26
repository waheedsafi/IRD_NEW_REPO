<?php

namespace App\Http\Controllers\api\app\ngo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Traits\Address\AddressTrait;


class NgoPublicController extends Controller
{
    //\


  public function ngos(Request $request, $page)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();

        $query =  DB::table('ngos as n')
            ->join('ngo_trans as nt', 'nt.ngo_id', '=', 'n.id')
            ->where('nt.language_name', $locale)
            ->join('ngo_statuses as ns', 'ns.ngo_id', '=', 'n.id')
            ->join('ngo_type_trans as ntt', 'ntt.ngo_type_id', '=', 'n.ngo_type_id')
            ->join('directors as dir','directors.ngo_id','ngos.id')
            ->join('director_trans ad dirt','dir.id','dirt.director_id')
            ->join('addresses add','add.id','ngos.address_id')
            ->join('status_type_trans as nstr', 'nstr.status_type_id', '=', 'ns.status_type_id')
            ->where('nstr.language_name', $locale)
            ->select(
                'n.id',
                'n.abbr',
                'n.registration_no',
                'n.date_of_establishment as establishment_date',
                'nstr.name as status',
                'nt.name',
                'ntt.value as type',
                'dirt.name as director_name',
                    $this->
                    'n.created_at'

            );


        $this->applyDate($query, $request);
        $this->applyFilters($query, $request);
        $this->applySearch($query, $request);

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'ngos' => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
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

        if ($searchColumn && $searchValue) {
            $allowedColumns = ['title', 'contents'];

            // Ensure that the search column is allowed
            if (in_array($searchColumn, $allowedColumns)) {
                $query->where($searchColumn, 'like', '%' . $searchValue . '%');
            }
        }
    }
    // filter function
    protected function applyFilters($query, $request)
    {
        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default 

        if ($sort && in_array($sort, ['id', 'name', 'type', 'contact', 'status'])) {
            $query->orderBy($sort, $order);
        } else {
            // Default sorting if no sort is provided
            $query->orderBy("created_at", 'desc');
        }
    }
}
