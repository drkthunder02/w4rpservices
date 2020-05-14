<?php

namespace App\Models\Market;

use Illuminate\Database\Eloquent\Model;

class MarketGroup extends Model
{
    //Table Name
    protected $table = 'market_groups';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'group',
        'description',
        'market_group_id',
        'name',
        'parent_group_id',
        'types',
    ];
}
