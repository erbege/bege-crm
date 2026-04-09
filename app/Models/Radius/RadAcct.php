<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

class RadAcct extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'radius';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'radacct';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'radacctid';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Scope a query to only include online users (acctstoptime is NULL).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnline($query)
    {
        return $query->whereNull('acctstoptime');
    }

    /**
     * Scope a query to only include hotspot users (exclude PPP).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHotspotOnly($query)
    {
        return $query->where(function ($q) {
            $q->where('framedprotocol', '!=', 'PPP')
                ->orWhereNull('framedprotocol')
                ->orWhere('framedprotocol', '');
        });
    }

    /**
     * Scope a query to sort by most recent session start.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('acctstarttime', 'desc');
    }

    public function voucher()
    {
        return $this->belongsTo(\App\Models\HotspotVoucher::class, 'username', 'code');
    }
}
