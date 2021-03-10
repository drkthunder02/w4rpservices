<?php

namespace App\Models\Lookups;

use Illuminate\Database\Eloquent\Model;

class ItemLookup extends Model
{
    /**
     * Table Name
     */
    public $table = 'item_lookup';

    //Primary Key
    public $primaryKey = 'id';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'capacity',
        'description',
        'graphic_id',
        'group_id',
        'icon_id',
        'market_group_id',
        'mass',
        'name',
        'packaged_volume',
        'portion_size',
        'published',
        'radius',
        'type_id',
        'volume',
    ];
}
