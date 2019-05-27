<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    /**
     * Required scope:
     * esi-assets.read_corporation_assets.v1
     */

    //Table Name
    public $table = 'alliance_asset';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'is_blueprint_copy',
        'is_singleton',
        'item_id',
        'location_flag',
        'location_id',
        'location_type',
        'quantity',
        'type_id',
    ];
}
