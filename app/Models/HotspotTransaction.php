<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotspotTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'hotspot_profile_id',
        'hotspot_voucher_id',
        'customer_name',
        'customer_contact',
        'amount',
        'status',
        'payment_method',
        'external_reference',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationship to HotspotProfile
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(HotspotProfile::class, 'hotspot_profile_id');
    }

    /**
     * Relationship to HotspotVoucher (assigned after successful payment)
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(HotspotVoucher::class, 'hotspot_voucher_id');
    }

    /**
     * Generate unique reference number like HTC-YYYYMM-XXXX
     */
    public static function generateReferenceNumber(): string
    {
        $prefix = 'HTC';
        $yearMonth = now()->format('Ym');

        do {
            $random = strtoupper(bin2hex(random_bytes(4))); // 8 characters
            $reference = "{$prefix}-{$yearMonth}-{$random}";
        } while (self::where('reference_number', $reference)->exists());

        return $reference;
    }
}
