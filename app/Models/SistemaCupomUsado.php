<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SistemaCupomUsado extends Model
{
    use HasFactory;

    protected $table = 'sistema_cupons_usados';

    protected $fillable = [
        'sistema_cupom_id',
        'usuario_id',
        'pedido_id',
    ];

    /**
     * Relacionamento com cupom do sistema
     */
    public function cupom()
    {
        return $this->belongsTo(SistemaCupom::class, 'sistema_cupom_id');
    }

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relacionamento com pedido (opcional)
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
}