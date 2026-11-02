<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminReportService;

class AdminReportController extends Controller
{
    public function overview(AdminReportService $service)
    {
        return view('admin.reports.overview', [
            'summary' => $service->getOverviewSummary(),
            'revenueTrend' => $service->getRevenueTrend(),
            'subscriptionBreakdown' => $service->getSubscriptionBreakdown(),
            'gymGrowth' => $service->getGymGrowth(),
        ]);
    }
}
