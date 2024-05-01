<?php

namespace App\Http\Controllers;

use App\Charts\MonthlyAmount;
use App\Models\LoanDetail;
use App\Models\OtherTransaction;
use App\Models\PrimarySaving;
use App\Models\PrimarySavingDetail;
use App\Models\SecondarySaving;
use App\Models\SecondarySavingDetail;
use App\Models\User;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function dashboard(MonthlyAmount $monthlyAmount)
    {
        $user = User::getUserLogin(Auth::id());
        $role = Auth::user()->role;

        // Mengubah pemformatan tanggal awal
        $start = Carbon::parse('2023-01-01')->startOfMonth()->format('Y-m-d');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d');
        // dd($start);

        // Menghitung current_amount sesuai dengan rentang tanggal yang sama dengan data bulanan
        $current_amount = (OtherTransaction::getSUMDebit($start, $end) +
                            PrimarySavingDetail::getSUMDebit($start, $end) +
                            SecondarySavingDetail::getSUMDebit($start, $end) +
                            LoanDetail::getSUMDebit($start, $end)) -
                        (OtherTransaction::getSUMCredit($start, $end) +
                            PrimarySavingDetail::getSUMCredit($start, $end) +
                            SecondarySavingDetail::getSUMCredit($start, $end) +
                            LoanDetail::getSUMCredit($start, $end));

        $data = $this->getMonthlyData($start, $end);
        // dd($data);
        $chart = $monthlyAmount->build()
            ->setWidth(1200)
            ->setHeight(400)
            ->addData('Debit', $data['debits'])
            ->addData('Kredit', $data['credits'])
            ->setXAxis($data['labels']);

        return view('pages.admin.dashboard', compact('user', 'role', 'current_amount', 'chart'));
    }


    private function getMonthlyData($start, $end)
    {
        $monthlyLabels = [];
        $monthlyDebits = [];
        $monthlyCredits = [];

        $date = $start;
        while ($date <= $end) {
            $monthlyLabels[] = Carbon::parse($date)->format('F Y');

            $debit = (OtherTransaction::getSUMDebit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()) +
                PrimarySavingDetail::getSUMDebit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()) +
                SecondarySavingDetail::getSUMDebit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()) +
                LoanDetail::getSUMDebit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()));

            $credit = (OtherTransaction::getSUMCredit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()) +
                PrimarySavingDetail::getSUMCredit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()) +
                SecondarySavingDetail::getSUMCredit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()) +
                LoanDetail::getSUMCredit(Carbon::parse($date)->startOfMonth(), Carbon::parse($date)->endOfMonth()));

            $monthlyDebits[] = $debit;
            $monthlyCredits[] = $credit;

            $date = Carbon::parse($date)->addMonth()->format('Y-m-d');
        }

        return [
            'labels' => $monthlyLabels,
            'debits' => $monthlyDebits,
            'credits' => $monthlyCredits,
        ];
    }




    // private function createChart($monthlyData)
    // {
    //     $chart = (new LarapexChart)->areaChart()
    //         ->setTitle('Monthly Transaction')
    //         ->setXAxis(array_keys($monthlyData))
    //         ->setDataset([
    //             [
    //                 'name' => 'Debit',
    //                 'data' => array_map(function ($data) {
    //                     return $data['debit'];
    //                 }, $monthlyData),
    //             ],
    //             [
    //                 'name' => 'Credit',
    //                 'data' => array_map(function ($data) {
    //                     return $data['credit'];
    //                 }, $monthlyData),
    //             ],
    //         ]);

    //     return $chart;
    // }
}
