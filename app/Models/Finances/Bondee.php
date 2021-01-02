<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;

class Bondee extends Model
{
    protected $table = 'alliance_bondees';

    protected $fillable = [
        'character_id',
        'character_name',
        'corporation_id',
        'corporation_name',
        'bond_id',
    ];

    public function allianceBond() {
        return $this->belongsTo(AllianceBond::class);
    }
}
