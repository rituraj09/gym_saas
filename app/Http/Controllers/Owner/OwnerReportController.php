<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Services\ReportService;
use Illuminate\Http\Request;

class OwnerReportController extends Controller
{
    protected function getReportService(): ReportService
    {
        $gym = auth()->user()->gymOwner->gym;
        return new ReportService($gym);
    }

    public function overview()
    {
        $service = $this->getReportService();

        return view('owner.reports.overview', [
            'summary' => $service->getOverviewSummary(),
            'revenueTrend' => $service->getRevenueTrend(),
            'membershipBreakdown' => $service->getMembershipBreakdown(),
        ]);
    }

    public function revenue(Request $request)
    {
        $service = $this->getReportService();
        $report = $service->getRevenueReport(
            $request->input('from'),
            $request->input('to'),
            $request->input('method_id')
        );
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view('owner.reports.revenue', compact('report', 'paymentMethods'));
    }

    public function membership(Request $request)
    {
        $service = $this->getReportService();
        $report = $service->getMembershipReport(
            $request->input('from'),
            $request->input('to')
        );

        return view('owner.reports.membership', compact('report'));
    }

    public function activity(Request $request)
    {
        $service = $this->getReportService();
        $report = $service->getActivityReport(
            $request->input('from'),
            $request->input('to')
        );

        return view('owner.reports.activity', compact('report'));
    }
}
