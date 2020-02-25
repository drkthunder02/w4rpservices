<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class AllianceMoonRequest extends Model
{
    // Table Name
    protected $table = 'alliance_moon_requests';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * The fillable items for each entry
     * 
     * @var array
     */
    protected $fillable = [
        'region',
        'system',
        'planet',
        'moon',
        'corporation_name',
        'corporation_ticker',
        'corporation_id',
        'requestor_name',
        'requestor_id',
        'approver_name',
        'approver_id',
        'status',
    ];
}
