<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Company extends Model {
    protected $table = 'company';
    protected $fillable = [
        'name','address','phone','email','paynow','bank_name','bank_account','cheque_name',
        'conclusion_text','signatory_name','signatory_title','important_clauses','signature_path','stamp_path'
    ];
}
