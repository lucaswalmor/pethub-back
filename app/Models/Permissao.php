<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permissao extends Model
{
    use HasFactory;

    protected $table = 'permissoes';

    // Relação com usuários
    public function usuarios()
    {
        return $this->hasMany(User::class, 'permissao_id');
    }
}
