<?php

namespace App\Traits\Helper;

use Illuminate\Support\Arr;

trait FilterTrait
{


    protected function applyDate($query, $request, $startDateColumn, $endDateColumn)
    {
        // Apply date filtering conditionally if provided
        $startDate = $request->input('filters.date.startDate');
        $endDate = $request->input('filters.date.endDate');

        if ($startDate) {
            $query->where($startDateColumn, '>=', $startDate);
        }
        if ($endDate) {
            $query->where($endDateColumn, '<=', $endDate);
        }
    }



    // search function 
    protected function applySearch($query, $request, array $allowColumns)
    {
        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = $allowColumns;
            // Ensure that the search column is allowed
            if (in_array($searchColumn, array_keys($allowedColumns))) {
                $query->where($allowedColumns[$searchColumn], 'like', '%' . $searchValue . '%');
            }
        }
    }





    // filter function
    protected function applyFilters($query, $request, array $allowColumns)
    {
        $sort = $request->input('filters.sort'); // Sorting column
        $order = $request->input('filters.order', 'asc'); // Sorting order (default 
        $allowedColumns = $allowColumns;

        if (in_array($sort, array_keys($allowedColumns))) {
            $query->orderBy($allowedColumns[$sort], $order);
        }
    }
}
