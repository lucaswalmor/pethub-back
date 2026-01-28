<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsuarioEnderecos extends Model
{
    use HasFactory;

    protected $table = 'usuarios_enderecos';

    protected $fillable = [
        'usuario_id',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'ponto_referencia',
        'observacoes',
        'ativo',
        'endereco_padrao',
    ];

    // Relação com usuário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
