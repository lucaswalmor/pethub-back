<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaBairrosEntregas extends Model
{
    use HasFactory;

    protected $table = 'empresa_bairros_entregas';

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relação com bairro
    public function bairro()
    {
        return $this->belongsTo(Bairro::class, 'bairro_id');
    }
}
