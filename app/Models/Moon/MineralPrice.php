<?php

namespace App\Models\Moon;

use Illuminate\Database\Eloquent\Model;

class MineralPrice extends Model
{
    // Table Name
    protected $table = 'Prices';

    //Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    /**
     * Fillable Array
     * 
     * @var array
     */
    protected $fillable = [
        'Name',
        'ItemId',
        'Price',
        'Time',
    ];
}
