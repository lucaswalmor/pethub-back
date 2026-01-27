<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaCupom extends Model
{
    use HasFactory;

    protected $table = 'empresa_cupons';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'tipo',
        'valor',
        'valor_minimo',
        'data_inicio',
        'data_fim',
        'limite_uso',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_minimo' => 'decimal:2',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relacionamento com usos do cupom
     */
    public function usos()
    {
        return $this->hasMany(EmpresaCupomUsado::class, 'empresa_cupom_id');
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
     * Scope para cupons disponíveis (ainda não atingiram limite)
     */
    public function scopeDisponiveis($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('limite_uso')
              ->orWhereRaw('limite_uso > (SELECT COUNT(*) FROM empresa_cupons_usados WHERE empresa_cupom_id = empresa_cupons.id)');
        });
    }

    /**
     * Scope para cupons de uma empresa específica
     */
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Verificar se cupom está válido
     */
    public function isValido()
    {
        if (!$this->ativo) return false;
        if (now()->lt($this->data_inicio)) return false;
        if (now()->gt($this->data_fim)) return false;

        // Verificar limite de uso
        if ($this->limite_uso !== null) {
            $usos = $this->usos()->count();
            if ($usos >= $this->limite_uso) return false;
        }

        return true;
    }

    /**
     * Calcular valor do desconto
     */
    public function calcularDesconto($valorCompra)
    {
        if (!$this->isValido()) return 0;

        // Verificar valor mínimo
        if ($this->valor_minimo && $valorCompra < $this->valor_minimo) {
            return 0;
        }

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
}