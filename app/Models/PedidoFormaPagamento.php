<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoFormaPagamento extends Model
{
    use HasFactory;

    protected $table = 'pedido_forma_pagamento';

    // Relação com pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
    
    // Relação com forma de pagamento
    public function formaPagamento()
    {
        return $this->belongsTo(FormasPagamentos::class, 'forma_pagamento_id');
    }
}
    