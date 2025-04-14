<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Model
{
    use HasApiTokens;

    protected $table = 't_pengguna';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; 

    protected $fillable = [
        'username', 'email', 'password', 'nama', 'telepon', 'foto', 'status', 'tanggal'
    ];

    public function rolePengguna()
    {
        return $this->hasOne(RolePengguna::class, 'username', 'username');
    }

    public function layerGroups()
    {
        return $this->hasMany(LayerGroup::class, 'username', 'username');
    }

    public function getFotoAttribute($value)
{
    return $value ? url(Storage::url($value)) : null;
}
}
