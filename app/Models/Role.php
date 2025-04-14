<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 't_role';
    protected $primaryKey = 'id_role';

    protected $fillable = [
        'role', 'aktif'
    ];

    public function rolePengguna()
    {
        return $this->hasMany(RolePengguna::class, 'id_role', 'id_role');
    }
}
