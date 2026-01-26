<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoItems extends Model
{
    use HasFactory;
    protected $table = 'pedido_items';

    // Relação com pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relação com produto
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
