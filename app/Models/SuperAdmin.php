<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $table = 'super_admin';

    protected $primaryKey = 'user_id';
    public $incrementing = false;  // Karena primary key menggunakan user_id yang bukan auto increment
    protected $fillable = ['user_id', 'employee_number'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignRole()
    {
        $user = $this->user;
        $role = Role::where('name', 'super_admin')->first();
        $user->roles()->attach($role);
    }
}
