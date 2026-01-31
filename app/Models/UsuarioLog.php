<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsuarioLog extends Model
{
    use HasFactory;

    protected $table = 'usuario_logs';

    protected $fillable = [
        'usuario_id',
        'empresa_id',
        'acao',
        'produto_id',
        'dados_adicionais',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'dados_adicionais' => 'array',
        'usuario_id' => 'integer', // Garante que seja sempre integer
    ];

    // Relacionamento com usuário (opcional)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Relacionamento com produto (opcional)
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    // Constantes para as ações
    const ACAO_VISUALIZAR_LOJA = 'visualizar_loja';
    const ACAO_ADICIONAR_CARRINHO = 'adicionar_carrinho';
    const ACAO_REMOVER_CARRINHO = 'remover_carrinho';
    const ACAO_ALTERAR_CARRINHO = 'alterar_carrinho';
    const ACAO_ACESSAR_LOJA_FECHADA = 'acessar_loja_fechada';
}
