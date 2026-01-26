<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusPedidos extends Model
{
    use HasFactory;

    protected $table = 'status_pedidos';

    // Relação com pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'status_pedido_id');
    }

    // Relação com histórico de status do pedido
    public function pedidosHistoricoStatus()
    {
        return $this->hasMany(PedidoHistoricoStatus::class, 'status_pedido_id');
    }
}
