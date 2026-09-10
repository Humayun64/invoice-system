<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
    protected $fillable = [
        'invoice_no','client_id','project_title','invoice_date',
        'show_qty','show_deposit','deposit_percent','deposit_label',
        'show_discount','discount_label','discount_type','discount_value','status'
    ];
    protected $casts = ['invoice_date' => 'date'];

    public function client() { return $this->belongsTo(Client::class); }
    public function items()  { return $this->hasMany(InvoiceItem::class)->orderBy('item_order'); }

    public function discountAmount(float $subtotal): float {
        if (!$this->show_discount) return 0;
        return $this->discount_type === 'percent'
            ? round($subtotal * (float)$this->discount_value / 100, 2)
            : (float)$this->discount_value;
    }
}
