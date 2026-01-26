<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaAssinatura extends Model
{
    use HasFactory;

    protected $table = 'empresa_assinatura';

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relação com plano
    public function plano()
    {
        return $this->belongsTo(Plano::class, 'plano_id');
    }
}
