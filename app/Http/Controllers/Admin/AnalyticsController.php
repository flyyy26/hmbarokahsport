<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Inisialisasi Client dengan file kredensial
        $client = new BetaAnalyticsDataClient([
            'credentials' => storage_path('app/analytics/google-credentials.json')
        ]);

        // 2. Siapkan Request
        $request = (new RunReportRequest())
            ->setProperty('properties/' . env('GA4_PROPERTY_ID')) // Ambil Property ID dari .env
            ->setDateRanges([
                new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']),
            ])
            ->setMetrics([
                new Metric(['name' => 'activeUsers']),
                new Metric(['name' => 'sessions']),
            ])
            ->setDimensions([
                new Dimension(['name' => 'date']),
            ]);

        // 3. Jalankan Query
        $response = $client->runReport($request);

        // 4. Olah Data untuk View
        $analyticsData = [];
        foreach ($response->getRows() as $row) {
            $analyticsData[] = [
                'date' => $row->getDimensionValues()[0]->getValue(),
                'activeUsers' => $row->getMetricValues()[0]->getValue(),
                'sessions' => $row->getMetricValues()[1]->getValue(),
            ];
        }

        // 5. Kirim ke View
        return view('admin.analytics.index', compact('analyticsData'));
    }
}
