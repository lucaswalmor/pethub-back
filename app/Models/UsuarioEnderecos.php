<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsuarioEnderecos extends Model
{
    use HasFactory;

    protected $table = 'usuarios_enderecos';

    // Relação com usuário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
