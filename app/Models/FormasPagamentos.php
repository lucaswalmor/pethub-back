<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormasPagamentos extends Model
{
    use HasFactory;

    protected $table = 'formas_pagamentos';

    // Relação com empresas de pagamento
    public function empresasPagamento()
    {
        return $this->hasMany(EmpresaFormasPagamentos::class, 'forma_pagamento_id');
    }
}
