<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminGudang extends Model
{
    use HasFactory;

    protected $table = 'admin_gudang';

    protected $primaryKey = 'user_id';
    public $incrementing = false;  // Karena primary key menggunakan user_id yang bukan auto increment
    protected $fillable = ['user_id', 'warehouse_code', 'employee_number'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignRole()
    {
        $user = $this->user;
        $role = Role::where('name', 'admin_gudang')->first();
        $user->roles()->attach($role);
    }
}
