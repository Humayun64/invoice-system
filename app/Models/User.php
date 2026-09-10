<?php
// app/Models/User.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $fillable = ['username','password','full_name','role'];
    protected $hidden   = ['password'];
}
