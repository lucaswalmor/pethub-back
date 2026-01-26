<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaFormasPagamentos extends Model
{
    use HasFactory;

    protected $table = 'empresa_formas_pagamentos';

    protected $fillable = [
        'empresa_id',
        'forma_pagamento_id',
        'ativo',
    ];

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relação com forma de pagamento
    public function formaPagamento()
    {
        return $this->belongsTo(FormasPagamentos::class, 'forma_pagamento_id');
    }
}
