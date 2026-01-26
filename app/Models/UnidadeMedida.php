<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnidadeMedida extends Model
{
    use HasFactory;

    protected $table = 'unidades_medidas';

    // Relação com produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'unidade_medida_id');
    }
}
