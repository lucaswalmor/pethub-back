<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plano extends Model
{
    use HasFactory;

    protected $table = 'planos';

    // Relação com empresas de assinatura
    public function empresasAssinatura()
    {
        return $this->hasMany(EmpresaAssinatura::class, 'plano_id');
    }
}
