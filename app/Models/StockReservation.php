<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StockReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'produk_id',
        'quantity',
        'type',
        'expires_at',
        'session_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeSoft($query)
    {
        return $query->where('type', 'soft');
    }

    public function scopeHard($query)
    {
        return $query->where('type', 'hard');
    }

    // Static methods untuk manajemen stok
    public static function getTotalReserved($produk_id)
    {
        return self::where('produk_id', $produk_id)
            ->active()
            ->sum('quantity');
    }

    public static function getAvailableStock($produk_id)
    {
        $produk = Produk::find($produk_id);
        if (!$produk) return 0;

        $reserved = self::getTotalReserved($produk_id);
        return max(0, $produk->stok - $reserved);
    }

    public static function cleanExpiredReservations()
    {
        return self::expired()->delete();
    }

    // Create atau update soft reservation
    public static function createSoftReservation($user_id, $produk_id, $quantity, $session_id = null)
    {
        // Clean expired first
        self::cleanExpiredReservations();

        // Check available stock
        $available = self::getAvailableStock($produk_id);
        if ($quantity > $available) {
            return false;
        }

        // Find existing soft reservation for this user and product
        $existing = self::where('user_id', $user_id)
            ->where('produk_id', $produk_id)
            ->where('type', 'soft')
            ->active()
            ->first();
        $expires_at  = now('Asia/Makassar')->addMinutes(40);
        if ($existing) {
            // Update existing reservation
            $newQuantity = $existing->quantity + $quantity;

            // Check if new quantity exceeds available stock
            $currentReserved = self::getTotalReserved($produk_id) - $existing->quantity;
            $produk = Produk::find($produk_id);
            $available = $produk->stok - $currentReserved;

            if ($newQuantity > $available) {
                return false;
            }
            // Extend expiry to 40 minutes
            $existing->update([
                'quantity' => $newQuantity,
                'expires_at' => $expires_at, // Extend expiry
                'session_id' => $session_id
            ]);

            return $existing;
        } else {
            // Create new reservation
            return self::create([
                'user_id' => $user_id,
                'produk_id' => $produk_id,
                'quantity' => $quantity,
                'type' => 'soft',
                'expires_at' => $expires_at, // 40 minutes for cart
                'session_id' => $session_id
            ]);
        }
    }

    // Convert soft to hard reservation (saat checkout)
    public static function convertToHardReservation($user_id, $produk_id, $quantity)
    {
        // Clean expired first
        self::cleanExpiredReservations();

        // Find soft reservation
        $softReservation = self::where('user_id', $user_id)
            ->where('produk_id', $produk_id)
            ->where('type', 'soft')
            ->active()
            ->first();

        if (!$softReservation || $softReservation->quantity < $quantity) {
            return false;
        }
        $expires_at = now('Asia/Makassar')->addMinutes(20); // 20 minutes for payment
        // Create hard reservation
        $hardReservation = self::create([
            'user_id' => $user_id,
            'produk_id' => $produk_id,
            'quantity' => $quantity,
            'type' => 'hard',
            'expires_at' => $expires_at, // 20 minutes for payment
            'session_id' => $softReservation->session_id
        ]);

        // Update soft reservation quantity or delete if zero
        if ($softReservation->quantity == $quantity) {
            $softReservation->delete();
        } else {
            $softReservation->update([
                'quantity' => $softReservation->quantity - $quantity
            ]);
        }

        return $hardReservation;
    }

    // Release reservation (after payment success or failure)
    public static function releaseReservation($user_id, $produk_id, $type = 'hard')
    {
        return self::where('user_id', $user_id)
            ->where('produk_id', $produk_id)
            ->where('type', $type)
            ->delete();
    }
}
