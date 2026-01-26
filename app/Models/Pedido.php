<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    // Relação com usuário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relação com status do pedido
    public function statusPedido()
    {
        return $this->belongsTo(StatusPedidos::class, 'status_pedido_id');
    }

    // Relação com forma de pagamento
    public function formaPagamento()
    {
        return $this->belongsTo(FormasPagamentos::class, 'pagamento_id');
    }

    // Relação com itens do pedido
    public function itens()
    {
        return $this->hasMany(PedidoItems::class, 'pedido_id');
    }

    // Relação com endereço do pedido
    public function endereco()
    {
        return $this->hasOne(PedidoEndereco::class, 'pedido_id');
    }

    // Relação com histórico de status do pedido
    public function historicoStatus()
    {
        return $this->hasMany(PedidoHistoricoStatus::class, 'pedido_id');
    }
}
