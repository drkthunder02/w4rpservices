<?php

namespace App\Models\AfterActionReports;

use Illuminate\Database\Eloquent\Model;

class AfterActionReportComment extends Model
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
        'report_id',
        'character_id',
        'character_name',
        'comments',
    ];

    public function report() {
        $this->belongsTo(App\Models\AfterActionReports\AfterActionReport::class, 'id', 'report_id');
    }
}
