<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaResgateCupom extends Model
{
    use HasFactory;

    protected $table = 'empresa_resgates_cupons';

    protected $fillable = [
        'empresa_id',
        'sistema_cupom_usado_id',
        'pedido_id',
        'empresa_usuario_id',
        'valor',
        'status',
        'data_solicitacao',
        'data_pagamento',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_solicitacao' => 'datetime',
        'data_pagamento' => 'datetime',
    ];

    /**
     * Relacionamento com empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relacionamento com o uso do cupom do sistema
     */
    public function sistemaCupomUsado()
    {
        return $this->belongsTo(SistemaCupomUsado::class, 'sistema_cupom_usado_id');
    }

    /**
     * Relacionamento com o pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Relacionamento com o usuário da empresa que solicitou (opcional)
     */
    public function empresaUsuario()
    {
        return $this->belongsTo(User::class, 'empresa_usuario_id');
    }
}
