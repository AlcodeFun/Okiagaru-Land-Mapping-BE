<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayerGroup extends Model
{
    protected $table = 't_layer_groups';
    protected $primaryKey = 'id_layer_groups';
    public $timestamps = false;

    protected $fillable = [
        'username', 'layer_groups', 'deskripsi', 'aktif'
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'username', 'username');
    }

    // Relasi dengan model Lahan
    public function lahans()
    {
        return $this->hasMany(Lahan::class, 'id_layer_groups', 'id_layer_groups');
    }
}
