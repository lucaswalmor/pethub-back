<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'slug',
        'email',
        'telefone',
        'cnpj',
        'nicho_id',
        'ativo',
    ];

    // Relação com nicho
    public function nicho()
    {
        return $this->belongsTo(NichosEmpresa::class, 'nicho_id');
    }

    // Relação com endereço
    public function endereco()
    {
        return $this->hasOne(EmpresaEndereco::class, 'empresa_id');
    }

    // Relação com configurações
    public function configuracoes()
    {
        return $this->hasOne(EmpresaConfiguracoes::class, 'empresa_id');
    }

    // Relação com horários
    public function horarios()
    {
        return $this->hasMany(EmpresaHorarios::class, 'empresa_id');
    }

    // Relação com bairros de entrega
    public function bairrosEntregas()
    {
        return $this->hasMany(EmpresaBairrosEntregas::class, 'empresa_id');
    }

    // Relação com assinatura
    public function assinatura()
    {
        return $this->hasOne(EmpresaAssinatura::class, 'empresa_id');
    }

    // Relação com formas de pagamento
    public function formasPagamentos()
    {
        return $this->hasMany(EmpresaFormasPagamentos::class, 'empresa_id');
    }

    // Relação com usuários
    public function usuarios()
    {
        return $this->hasMany(UsuarioEmpresas::class, 'empresa_id');
    }

    // Relação com produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'empresa_id');
    }

    // Relação com pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'empresa_id');
    }
}
