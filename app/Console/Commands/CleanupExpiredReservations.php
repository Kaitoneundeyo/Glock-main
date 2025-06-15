<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockReservation;
use App\Models\Order;

class CleanupExpiredReservations extends Command
{
    protected $signature = 'reservations:cleanup';
    protected $description = 'Clean up expired stock reservations and cancel unpaid orders';

    public function handle()
    {
        $this->info('Starting cleanup of expired reservations...');

        // Clean expired reservations
        $expiredReservations = StockReservation::expired()->count();
        StockReservation::cleanExpiredReservations();

        $this->info("Cleaned {$expiredReservations} expired reservations.");

        // Cancel orders with expired hard reservations
        $expiredOrders = Order::where('status', 'pending_payment')
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        foreach ($expiredOrders as $order) {
            // Check if user has any active hard reservations
            $hasActiveReservations = StockReservation::where('user_id', $order->user_id)
                ->where('type', 'hard')
                ->active()
                ->exists();

            if (!$hasActiveReservations) {
                $order->update(['status' => 'cancelled']);
                $this->info("Cancelled order: {$order->order_number}");
            }
        }

        $this->info('Cleanup completed successfully!');
        return 0;
    }
}

// Register this command in app/Console/Kernel.php
// Add to the $commands array:
// Commands\CleanupExpiredReservations::class,

// And schedule it to run every minute in the schedule method:
// $schedule->command('reservations:cleanup')->everyMinute();
