<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model {
    protected $fillable = [
        'quotation_no','client_id','project_title','quotation_date',
        'valid_days','intro_text','show_qty','show_discount','discount_label','discount_type','discount_value',
        'payment_terms','important_clauses','price_basis','conclusion','status'
    ];
    protected $casts = ['quotation_date' => 'date'];

    public function client() { return $this->belongsTo(Client::class); }
    public function items()  { return $this->hasMany(QuotationItem::class)->orderBy('item_order'); }

    public function discountAmount(float $subtotal): float {
        if (!$this->show_discount) return 0;
        return $this->discount_type === 'percent'
            ? round($subtotal * (float)$this->discount_value / 100, 2)
            : (float)$this->discount_value;
    }
}
