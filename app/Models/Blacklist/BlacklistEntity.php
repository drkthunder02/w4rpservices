<?php

namespace App\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class BlacklistEntity extends Model
{
    //Table Name
    public $table = 'alliance_blacklist';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'entity_name',
        'entity_type',
        'reason',
        'alts',
        'lister_id',
        'lister_name',
    ];
}
