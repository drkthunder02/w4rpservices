<?php

namespace App\Models\Flex;

use Illuminate\Database\Eloquent\Model;

class FlexStructure extends Model
{
    /**
     * Table Name
     */
    public $table = 'alliance_flex_structures';

    /**
     * Timestamps
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'requestor_id',
        'requestor_name',
        'requestor_corp_id',
        'requestor_corp_name',
        'system_id',
        'system',
        'structure_type',
        'structure_cost',
    ];
}
