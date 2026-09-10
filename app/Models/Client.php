<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    protected $fillable = ['name','designation','company','address','postal_code','phone','email'];

    public function invoices()   { return $this->hasMany(Invoice::class); }
    public function quotations() { return $this->hasMany(Quotation::class); }
}
