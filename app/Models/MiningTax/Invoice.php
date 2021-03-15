<?php

namespace App\Models\MiningTax;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //Table Name
    protected $table = 'alliance_mining_tax_invoices';

    //Primary Key
    public $primaryKey = 'id';

    //Timestamps
    public $timestamps = true;

    /**
     * Items which are mass assignable
     * 
     * @var array
     */
    protected $fillable = [
        'character_id',
        'character_name',
        'invoice_id',
        'invoice_amount',
        'date_issued',
        'date_due',
        'status',
        'mail_body',
    ];

    public function getPayment() {
        return $this->hasOne(App\Models\MiningTax\Payment::class, 'invoice_id', 'invoice_id');
    }

    public function getCharacterId() {
        return $this->character_id;
    }

    public function getCharacterName() {
        return $this->character_name;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getLedgers() {
        return $this->hasMany(App\Models\MiningTax\Ledger::class, 'invoice_id', 'invoice_id');
    }

    public function getInvoiceAmount() {
        return $this->invoice_amount;
    }
}
