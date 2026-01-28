<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoHistoricoStatus extends Model
{
    use HasFactory;

    protected $table = 'pedido_historico_status';

    protected $fillable = [
        'pedido_id',
        'status_pedido_id',
        'observacoes',
    ];

    // Relação com pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
    
    // Relação com status do pedido
    public function statusPedido()
    {
        return $this->belongsTo(StatusPedidos::class, 'status_pedido_id');
    }
}
