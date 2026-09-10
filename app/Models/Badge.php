<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model {
    protected $fillable = ['position','image_path','sort_order'];
}
