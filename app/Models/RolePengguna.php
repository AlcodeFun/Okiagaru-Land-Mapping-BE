<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePengguna extends Model
{
    protected $table = 't_role_pengguna';
    protected $primaryKey = 'id_role_pengguna';
    public $timestamps = false; 

    protected $fillable = [
        'username', 'id_role'
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'username', 'username');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }
}
