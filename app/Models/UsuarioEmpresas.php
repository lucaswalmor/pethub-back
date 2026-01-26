<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsuarioEmpresas extends Model
{
    use HasFactory;

    protected $table = 'usuarios_empresas';

    protected $fillable = [
        'usuario_id',
        'empresa_id',
    ];

    // Relação com usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
}
