<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class HomeController extends Controller
{


    public function index()
    {
        $salesData = DB::table('orders')
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [now()->subDays(6), now()])
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('date')
            ->get();

        // Format untuk chart.js
        $labels = $salesData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $data = $salesData->pluck('total')->toArray();

        return view('home', compact('labels', 'data'));
    }
}
