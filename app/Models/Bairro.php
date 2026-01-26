<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bairro extends Model
{
    use HasFactory;

    protected $table = 'bairros';

    // Relação com empresas de entrega
    public function empresasEntregas()
    {
        return $this->hasMany(EmpresaBairrosEntregas::class, 'bairro_id');
    }
}
