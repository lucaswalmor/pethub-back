<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produtos';

    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'unidade_medida_id',
        'tipo',
        'nome',
        'imagem',
        'slug',
        'descricao',
        'preco',
        'estoque',
        'destaque',
        'ativo',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'decimal:3',
        'destaque' => 'boolean',
        'ativo' => 'boolean',
    ];

    // Boot method para gerar slug automaticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($produto) {
            if (empty($produto->slug)) {
                $produto->slug = Str::slug($produto->nome);
            }
        });

        static::updating(function ($produto) {
            if ($produto->isDirty('nome') && empty($produto->slug)) {
                $produto->slug = Str::slug($produto->nome);
            }
        });
    }

    // Relação com itens do pedido
    public function itens()
    {
        return $this->hasMany(PedidoItems::class, 'produto_id');
    }

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relação com categoria
    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }

    // Relação com unidade de medida
    public function unidadeMedida()
    {
        return $this->belongsTo(UnidadeMedida::class, 'unidade_medida_id');
    }

    // Scope para produtos ativos
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Scope para produtos em destaque
    public function scopeDestaque($query)
    {
        return $query->where('destaque', true);
    }

    // Scope para filtrar por empresa
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Scope para filtrar por categoria
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // Scope para filtrar por tipo (produto/servico)
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Método para verificar se tem estoque disponível
    public function temEstoque($quantidade = 1)
    {
        return $this->estoque >= $quantidade;
    }

    // Método para reduzir estoque
    public function reduzirEstoque($quantidade)
    {
        if ($this->temEstoque($quantidade)) {
            $this->estoque -= $quantidade;
            $this->save();
            return true;
        }
        return false;
    }

    // Método para adicionar estoque
    public function adicionarEstoque($quantidade)
    {
        $this->estoque += $quantidade;
        $this->save();
        return true;
    }
}
