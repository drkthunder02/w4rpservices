<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiningOperation extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_operations';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * The array of variables which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'structure_id',
        'structure_name',
        'authorized_by_id',
        'authorized_by_name',
        'operation_date',
        'processed',
        'processed_on',
    ];
}
