<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaConfiguracoes extends Model
{
    use HasFactory;

    protected $table = 'empresa_configuracoes';

    protected $fillable = [
        'empresa_id',
        'faz_entrega',
        'faz_retirada',
        'a_combinar',
        'valor_entrega_padrao',
        'valor_entrega_minimo',
        'telefone_comercial',
        'celular_comercial',
        'whatsapp_pedidos',
        'email',
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
        'tiktok',
    ];

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
