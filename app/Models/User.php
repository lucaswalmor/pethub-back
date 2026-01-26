<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    protected $table = 'usuarios';
    
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'permissao_id',
        'nome',
        'email',
        'password',
        'telefone',
        'ativo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relação com endereços
    public function enderecos()
    {
        return $this->hasMany(UsuarioEnderecos::class, 'usuario_id');
    }

    // Relação com permissão
    public function permissao()
    {
        return $this->belongsTo(Permissao::class, 'permissao_id');
    }

    // Relação com empresas (através da tabela usuarios_empresas)
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'usuarios_empresas', 'usuario_id', 'empresa_id');
    }

    // Relação com pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }

    // Verifica se o usuário é administrador
    public function isAdmin()
    {
        return $this->permissao->slug === 'admin';
    }

    // Verifica se o usuário é vendedor
    public function isVendedor()
    {
        return $this->permissao->slug === 'vendedor';
    }

    // Verifica se o usuário é financeiro
    public function isFinanceiro()
    {
        return $this->permissao->slug === 'financeiro';
    }

    // Verifica se o usuário é cliente
    public function isCliente()
    {
        return $this->permissao->slug === 'cliente';
    }
}
