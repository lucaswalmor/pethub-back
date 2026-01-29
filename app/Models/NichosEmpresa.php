<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NichosEmpresa extends Model
{
    use HasFactory;

    protected $table = 'nichos_empresa';

    protected $fillable = [
        'nome',
        'slug',
        'imagem',
        'ativo'
    ];

    // Relação com empresas
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'nicho_id');
    }
}
