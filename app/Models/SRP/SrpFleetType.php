<?php

namespace App\Models\SRP;

use Illuminate\Database\Eloquent\Model;

class SrpFleetType extends Model
{
    //Table Name
    protected $table = 'srp_fleet_types';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'code',
        'description',
    ];
}
