<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaEndereco extends Model
{
    use HasFactory;

    protected $table = 'empresa_endereco';

    protected $fillable = [
        'empresa_id',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'ponto_referencia',
        'observacoes',
    ];

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
