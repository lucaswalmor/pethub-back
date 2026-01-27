<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaCupomUsado extends Model
{
    use HasFactory;

    protected $table = 'empresa_cupons_usados';

    protected $fillable = [
        'empresa_cupom_id',
        'usuario_id',
        'pedido_id',
    ];

    /**
     * Relacionamento com cupom
     */
    public function cupom()
    {
        return $this->belongsTo(EmpresaCupom::class, 'empresa_cupom_id');
    }

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relacionamento com pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Scope para usos recentes
     */
    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    /**
     * Scope para usos de uma empresa específica
     */
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->whereHas('cupom', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        });
    }
}