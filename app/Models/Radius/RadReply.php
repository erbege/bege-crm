<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

class RadReply extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'radius';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'radreply';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
    ];
}
