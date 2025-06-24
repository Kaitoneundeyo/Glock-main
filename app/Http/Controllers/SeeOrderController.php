<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class SeeOrderController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $search = $request->input('search'); // ambil input pencarian

        $orders = Order::with('orderItems.produk')
            ->whereDate('created_at', $tanggal)
            ->when($search, function ($query) use ($search) {
                $query->where('order_number', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('order.index', compact('orders', 'tanggal', 'search'));
    }
}
