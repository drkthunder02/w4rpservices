<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
     // Table Name
     public $table = 'contracts';

     // Timestamps
     public $timestamps = true;
 
     /**
      * The attributes that are mass assignable
      * 
      * @var array
      */
     protected $fillable = [
         'title',
         'end_date',
         'body',
     ];

     protected $guarded = [];

     public function Bids() {
         return $this->hasMany('App\Models\Contracts\Bid', 'contract_id', 'id');
     }
}
