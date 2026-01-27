<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SistemaCupom extends Model
{
    use HasFactory;

    protected $table = 'sistema_cupons';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'data_inicio',
        'data_fim',
        'limite_uso_total',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com usos do cupom
     */
    public function usos()
    {
        return $this->hasMany(SistemaCupomUsado::class, 'sistema_cupom_id');
    }

    /**
     * Relacionamento com usuários que têm este cupom
     */
    public function usuariosCupons()
    {
        return $this->hasMany(UsuarioCupom::class, 'sistema_cupom_id');
    }

    /**
     * Scope para cupons ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)
            ->where('data_inicio', '<=', now())
            ->where('data_fim', '>=', now());
    }

    /**
     * Scope para cupons disponíveis (ainda não atingiram limite total)
     */
    public function scopeDisponiveis($query)
    {
        return $query->whereRaw('limite_uso_total > (SELECT COUNT(*) FROM sistema_cupons_usados WHERE sistema_cupom_id = sistema_cupons.id)');
    }

    /**
     * Verificar se cupom está válido
     */
    public function isValido()
    {
        if (!$this->ativo) return false;
        if (now()->lt($this->data_inicio)) return false;
        if (now()->gt($this->data_fim)) return false;

        // Verificar limite de uso total
        $usos = $this->usos()->count();
        if ($usos >= $this->limite_uso_total) return false;

        return true;
    }

    /**
     * Calcular valor do desconto
     */
    public function calcularDesconto($valorCompra)
    {
        if (!$this->isValido()) return 0;

        if ($this->tipo === 'percentual') {
            return $valorCompra * ($this->valor / 100);
        } else {
            return min($this->valor, $valorCompra);
        }
    }

    /**
     * Verificar se usuário já usou este cupom
     */
    public function usuarioJaUsou($usuarioId)
    {
        return $this->usos()->where('usuario_id', $usuarioId)->exists();
    }

    /**
     * Verificar se usuário tem este cupom atribuído
     */
    public function usuarioTemCupom($usuarioId)
    {
        return $this->usuariosCupons()
            ->where('usuario_id', $usuarioId)
            ->whereNull('usado_em')
            ->exists();
    }
}