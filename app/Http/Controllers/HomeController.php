<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{


    public function index()
    {
        $startDate = Carbon::now()->subDays(13)->startOfDay(); // 14 hari termasuk hari ini
        $endDate = Carbon::now()->endOfDay();

        $sales = OrderItem::selectRaw('DATE(orders.paid_at) as tanggal, SUM(subtotal) as total')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'paid')
            ->whereBetween('orders.paid_at', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Siapkan range tanggal lengkap 14 hari terakhir
        $range = collect();
        for ($i = 0; $i < 14; $i++) {
            $tanggal = Carbon::now()->subDays(13 - $i)->format('Y-m-d');
            $range->put($tanggal, 0);
        }

        // Masukkan data yang tersedia ke range
        foreach ($sales as $item) {
            $range[$item->tanggal] = (float) $item->total;
        }

        // Format label ke d M
        $labels = $range->keys()->map(fn($date) => Carbon::parse($date)->format('d M'))->toArray();
        $data = $range->values()->toArray();

        return view('home', compact('labels', 'data'));
    }
}
