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
        'nome',
        'email',
        'password',
        'telefone',
        'ativo',
        'is_master',
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

    // Relação com empresas (através da tabela usuario_empresas)
    public function usuarioEmpresas()
    {
        return $this->hasMany(UsuarioEmpresas::class, 'usuario_id');
    }

    // Relação many-to-many com permissões
    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'usuarios_permissoes', 'usuario_id', 'permissao_id');
    }

    // Verifica se o usuário tem uma permissão específica
    public function hasPermission(string $slug): bool
    {
        // Se é master, pode tudo
        if ($this->isMaster()) {
            return true;
        }

        return $this->permissoes()->where('slug', $slug)->exists();
    }

    // Verifica se o usuário tem qualquer uma das permissões
    public function hasAnyPermission(array $slugs): bool
    {
        // Se é master, pode tudo
        if ($this->isMaster()) {
            return true;
        }

        return $this->permissoes()->whereIn('slug', $slugs)->exists();
    }

    // Verifica se o usuário é master da empresa
    public function isMaster()
    {
        return $this->is_master;
    }
}
