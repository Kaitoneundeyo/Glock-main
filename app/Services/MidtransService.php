<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Sale;

class MidtransService
{
    public function __construct()
    {
        // Konfigurasi Midtrans dari config/midtrans.php
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generate Snap Token dari objek Sale
     *
     * @param \App\Models\Sale $sale
     * @return string $snapToken
     */
    public function createSnapToken(Sale $sale)
    {
        $items = [];

        foreach ($sale->saleItems as $item) {
            $items[] = [
                'id' => $item->produk_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => $item->produk->nama_produk,
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $sale->invoice_number,
                'gross_amount' => (int) $sale->total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $sale->user->name,
                'email' => $sale->user->email,
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
