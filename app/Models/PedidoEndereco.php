<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoEndereco extends Model
{
    use HasFactory;

    protected $table = 'pedido_endereco';

    // Relação com pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relação com endereço
    public function endereco()
    {
        return $this->belongsTo(UsuarioEnderecos::class, 'endereco_id');
    }
}
