<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;

class AllianceBond extends Model
{
    /**
     * Table Name
     */
    protected $table = 'alliance_bonds';

    /**
     * Timestamps
     */
    public $timestamps = true;

    /**
     * The attributes which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'full_bond_amount',
        'remaining_bond_amount',
        'bond_id',
        'character_id',
    ];

    public function bonders() {
        return $this->hasMany('App\Models\Finances\Bondee', 'character_id', 'character_id');
    }

    public function getBondId() {
        return $this->bond_id;
    }

}
