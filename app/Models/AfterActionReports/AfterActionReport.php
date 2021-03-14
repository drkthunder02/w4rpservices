<?php

namespace App\Models\AfterActionreports;

use Illuminate\Database\Eloquent\Model;

class AfterActionReport extends Model
{
    //Table Name
    public $table = '';

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
        'fc_id',
        'fc_name',
        'formup_time',
        'formup_location',
        'comms',
        'doctrine',
        'objective',
        'objective_result',
        'summary',
        'improvements',
        'worked_well',
        'additional_comments',
    ];

    public function comments() {
        return $this->hasMany(App\Models\AfterActionReports\AfterActionReportComment::class, 'report_id', 'id');   
    }

    
}
