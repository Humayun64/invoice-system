<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model {
    protected $fillable = ['invoice_id','item_order','description','qty','unit','unit_price','amount'];
    public function invoice() { return $this->belongsTo(Invoice::class); }
}
