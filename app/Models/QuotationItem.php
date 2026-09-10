<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model {
    protected $fillable = ['quotation_id','item_order','description','qty','unit','unit_price','amount'];
    public function quotation() { return $this->belongsTo(Quotation::class); }
}
