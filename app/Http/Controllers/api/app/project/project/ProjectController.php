<?php

namespace App\Http\Controllers\api\app\project\project;

use Illuminate\Http\Request;
use App\Traits\Helper\FilterTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    use FilterTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();

        $query = DB::table('projects as pro')
            ->join('project_trans as prot', function ($join) use ($locale) {
                $join->on('pro.id', '=', 'prot.project_id')
                    ->where('prot.language_id', $locale);
            })
            ->join('donor_trans dont', function ($join) use ($locale) {
                $join->on('dont.donor_id', 'pro.donor_id')
                    ->where('dont.language_name', $locale);
            })
            ->select(
                'pro.id',
                'pro.total_budget',
                'pro.start_date',
                'pro.end_date',
                'pro.donor_registration_no',
                'prot.name as title',
                'dont.name as donar',
                'pro.created_at'




            );


        $this->applyDate($query, $request, 'pro.created_at', 'pro.created_at');
        $allowColumn = [
            'title' => 'prot.title',
            'donor' => 'dont.donar'
        ];
        $this->applyFilters($query, $request, $allowColumn);

        $this->applySearch($query, $request, $allowColumn);

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'project' => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
