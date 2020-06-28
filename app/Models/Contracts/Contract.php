<?php

namespace App\Models\Contracts;

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
         'issuer_id',
         'issuer_name',
         'issuer_corp_id',
         'issuer_corp_name',
         'title',
         'type',
         'end_date',
         'body',
         'final_cost',
         'finished',
     ];

     //One-to-Many relationship for the bids on a contract
     public function Bids() {
         return $this->hasMany('App\Models\Contracts\Bid', 'contract_id', 'id');
     }

     //One-to-One relationship for the accepted bid.
     public function AcceptedBid() {
         return $this->hasOne('App\Models\Contracts\AcceptedBid', 'contract_id', 'id');
     }
}
