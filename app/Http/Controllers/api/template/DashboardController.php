<?php

namespace App\Http\Controllers\api\template;

use App\Enums\Type\NgoTypeEnum;
use App\Enums\Type\StatusTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Ngo;
use App\Models\StatusType;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function dashboardInfo()
    {
        $locale = App::getLocale();

        // Fetch data using a stored procedure
        $results = $this->fetchDashboardData($locale);

        // Map the results
        $documentCountByStatus = $results[0] ?? [];
        $documentTypePercentages = $results[1] ?? [];
        $monthlyDocumentTypeCount = $results[2] ?? [];
        // $documentTypeSixMonths = $results[3] ?? [];
        $documentUrgencyCounts = $results[3] ?? [];
        $monthlyDocumentCounts = $results[4] ?? [];

        // return $monthlyDocumentTypeCount;

        if ($documentUrgencyCounts) {
        }
        // Process monthly document counts
        $monthlyData = $this->processMonthlyData($monthlyDocumentCounts);


        // Process grouped data for monthly type counts
        $groupedMonthlyTypeCounts = $this->groupMonthlyTypeCounts($monthlyDocumentTypeCount);

        // Process document type percentages
        $documentTypeData = $this->processDocumentTypePercentages($documentTypePercentages);


        return response()->json([
            'statuses' => $documentCountByStatus,
            'documentTypePercentages' => $documentTypeData,
            'montlyTypeCount' => $groupedMonthlyTypeCounts,
            'documentUrgencyCounts' => $documentUrgencyCounts,
            'monthlyDocumentCounts' => $monthlyData,
        ]);
    }

    private function fetchDashboardData(string $locale): array
    {
        $pdo = DB::getPdo();
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

        $stmt = $pdo->prepare('CALL GetDashboardData(:locale)');
        $stmt->bindParam(':locale', $locale);
        $stmt->execute();

        $results = [];
        do {
            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($resultSet) {
                $results[] = $resultSet;
            }
        } while ($stmt->nextRowset());

        return $results;
    }

    private function processMonthlyData(array $monthlyDocumentCounts): array
    {

        if ($monthlyDocumentCounts == []) {
            $monthlyDocumentCounts = ['document_count' => [0], 'month' => ['January']];
        }
        $allMonths = range(1, 12);
        $dataMap = array_column($monthlyDocumentCounts, 'document_count', 'month');

        $monthNames = [
            1 => "January",
            2 => "February",
            3 => "March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 => "December",
        ];

        $monthNamesArray = [];
        $monthCountsArray = [];

        foreach ($allMonths as $monthNum) {
            $monthNamesArray[] = $monthNames[$monthNum];
            $monthCountsArray[] = $dataMap[$monthNum] ?? 0;
        }

        return [$monthNamesArray, $monthCountsArray];
    }

    private function groupMonthlyTypeCounts(array $monthlyDocumentTypeCount): array
    {
        // Initialize an array for all months (1-12) with 0 counts
        $allMonths = range(1, 12);

        // Group data by document type
        $groupedData = [];

        foreach ($monthlyDocumentTypeCount as $entry) {
            $typeName = $entry['document_type_name'];
            $month = $entry['month'];
            $count = $entry['document_count'];

            // Ensure each document type has an array of 12 months initialized with 0
            if (!isset($groupedData[$typeName])) {
                $groupedData[$typeName] = array_fill(0, 12, 0);
            }

            // If a valid month is provided, increment the corresponding count
            if ($month !== null && $month >= 1 && $month <= 12) {
                $groupedData[$typeName][$month - 1] += $count;
            }
        }

        // Format the final result
        $finalResult = [];
        foreach ($groupedData as $typeName => $monthlyData) {
            $finalResult[] = [
                'document_type_name' => $typeName,
                'monthly_data' => $monthlyData, // Only the counts for each month
            ];
        }

        return $finalResult;
    }


    private function processDocumentTypePercentages(array $documentTypePercentages): array
    {
        $documentTypeNames = array_column($documentTypePercentages, 'document_type_name');
        $percentages = array_column($documentTypePercentages, 'percentage');

        return [$documentTypeNames, array_map('floatval', $percentages)];
    }

    public function headerData()
    {
        $counts = $this->ngoCountByTypes();
        return $this->ngoCountByStatus();
        return $this->ngoCountByTypesPerMonth();
        return [
            'internationalNgoCount' => $counts['international_count'],
            'domesticNgoCount' =>  $counts['domestic_count'],
            'intergovernmentalNgoCount' => $counts['intergovernmental_count'],
            'totalNgoCount' => $counts['international_count'] + $counts['domestic_count'] + $counts['intergovernmental_count'],
        ];
    }
    private function ngoCountByTypes()
    {
        $cacheKey = 'ngo_count_all_types';

        // Check if the counts are already cached
        $counts = Cache::remember($cacheKey, 180, function () {
            return DB::table('ngos')
                ->select(DB::raw('
                sum(ngo_type_id = ' . NgoTypeEnum::International->value . ') as international_count,
                sum(ngo_type_id = ' . NgoTypeEnum::Domestic->value . ') as domestic_count,
                sum(ngo_type_id = ' . NgoTypeEnum::Intergovernmental->value . ') as intergovernmental_count
            '))->first();
        });

        return [
            'international_count' => $counts->international_count,
            'domestic_count' => $counts->domestic_count,
            'intergovernmental_count' => $counts->intergovernmental_count,
        ];
    }



    private function ngoCountByTypesPerMonth()
    {
        $cacheKey = 'ngo_count_all_types_last_6_months';

        // Get the date 6 months ago
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        // Check if the counts are already cached
        $counts = Cache::remember($cacheKey, 60, function () use ($sixMonthsAgo) {
            return DB::table('ngos')
                ->select(DB::raw('
                     MONTH(created_at) as month,
                    sum(ngo_type_id = ' . NgoTypeEnum::International->value . ') as international_count,
                    sum(ngo_type_id = ' . NgoTypeEnum::Domestic->value . ') as domestic_count,
                    sum(ngo_type_id = ' . NgoTypeEnum::Intergovernmental->value . ') as intergovernmental_count
                '))
                ->where('created_at', '>=', $sixMonthsAgo) // Filter by the last 6 months
                ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->get();
        });

        // Create an array with the months for the last 6 months
        $months = collect();
        for ($i = 0; $i < 6; $i++) {
            $months->push(Carbon::now()->subMonths(6 - $i)->format('F'));
        }

        // Map the counts and fill missing months with zeros
        $monthlyCounts = $months->map(function ($monthName) use ($counts) {
            $monthData = $counts->firstWhere('month', Carbon::createFromFormat('F', $monthName)->month);

            return [
                'month' => $monthName,
                'intenational' => $monthData ? $monthData->international_count : 0,  // International count corresponds to desktop
                'domestic' => $monthData ? $monthData->domestic_count : 0,        // Domestic count corresponds to mobile
                'intergovermental' => $monthData ? $monthData->intergovernmental_count : 0, // Intergovernmental count corresponds to other
            ];
        });

        return $monthlyCounts;
    }

    private function ngoCountByStatus()
    {
        // Define the cache key
        $cacheKey = 'ngo_count_all_status_types';

        // Check if the counts are already cached
        $counts = Cache::remember($cacheKey, 60, function () {
            // Use StatusTypeEnum's values to ensure correctness
            return DB::table('ngo_statuses')
                ->select(DB::raw('
                    sum(status_type_id = ' . StatusTypeEnum::active->value . ') as active,
                    sum(status_type_id = ' . StatusTypeEnum::blocked->value . ') as blocked,
                    sum(status_type_id = ' . StatusTypeEnum::unregistered->value . ') as unregistered,
                    sum(status_type_id = ' . StatusTypeEnum::in_progress->value . ') as in_progress,
                    sum(status_type_id = ' . StatusTypeEnum::register_form_submited->value . ') as register_form_submited,
                    sum(status_type_id = ' . StatusTypeEnum::not_logged_in->value . ') as not_logged_in
                '))
                ->where('is_active', 1)
                ->first();
        });
        return $counts;

        // Return counts, defaulting to 0 if not available
        return [
            'active' => $counts->active ?? 0,
            'blocked' => $counts->blocked ?? 0,
            'unregistered' => $counts->unregistered ?? 0,
            'register_form_submited' => $counts->register_form_submited ?? 0,
            'not_logged_in' => $counts->not_logged_in ?? 0,
            'in_progress' => $counts->in_progress ?? 0,
        ];
    }
}
