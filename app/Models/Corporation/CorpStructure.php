<?php

namespace App\Models\Corporation;

use Illuminate\Database\Eloquent\Model;

class CorpStructure extends Model
{
    /**
     * Table Name
     */
    protected $table = 'CorpStructures';

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
        'character_id',
        'corporation_id',
        'corporation_name',
        'region',
        'system',
        'structure_name',
        'structure_type',
    ];
}
