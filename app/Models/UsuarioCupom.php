<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsuarioCupom extends Model
{
    use HasFactory;

    protected $table = 'usuarios_cupons';

    protected $fillable = [
        'usuario_id',
        'sistema_cupom_id',
        'usado_em',
        'pedido_id',
    ];

    protected $casts = [
        'usado_em' => 'datetime',
    ];

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relacionamento com cupom do sistema
     */
    public function cupom()
    {
        return $this->belongsTo(SistemaCupom::class, 'sistema_cupom_id');
    }

    /**
     * Relacionamento com pedido (opcional)
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Scope para cupons não utilizados
     */
    public function scopeNaoUtilizados($query)
    {
        return $query->whereNull('usado_em');
    }

    /**
     * Scope para cupons utilizados
     */
    public function scopeUtilizados($query)
    {
        return $query->whereNotNull('usado_em');
    }

    /**
     * Scope para cupons de um usuário específico
     */
    public function scopeDoUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para cupons válidos (não utilizados e cupom ativo)
     */
    public function scopeValidos($query)
    {
        return $query->naoUtilizados()
            ->whereHas('cupom', function ($q) {
                $q->ativos();
            });
    }

    /**
     * Marcar cupom como utilizado
     */
    public function marcarComoUtilizado($pedidoId = null)
    {
        $this->update([
            'usado_em' => now(),
            'pedido_id' => $pedidoId,
        ]);
    }

    /**
     * Verificar se cupom já foi utilizado
     */
    public function foiUtilizado()
    {
        return !is_null($this->usado_em);
    }

    /**
     * Obter valor do desconto do cupom
     */
    public function getValorDesconto($valorCompra = null)
    {
        if ($this->foiUtilizado() || !$this->cupom) {
            return 0;
        }

        return $this->cupom->calcularDesconto($valorCompra);
    }
}