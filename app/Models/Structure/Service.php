<?php

namespace App\Models\Structure;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //Table Name
    public $table = 'alliance_services';

    //Timestamps
    public $timestamps = true;

    //Primary Key
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'structure_id',
        'name',
        'state',
        'created_at',
        'updated_at',
    ];

    public function structure() {
        return $this->belongsTo(App\Models\Structure\Structure::class, 'structure_id', 'structure_id');
    }
}
