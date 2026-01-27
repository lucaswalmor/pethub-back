<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permissao extends Model
{
    use HasFactory;

    protected $table = 'permissoes';

    // Relação many-to-many com usuários
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuarios_permissoes', 'permissao_id', 'usuario_id');
    }
}
