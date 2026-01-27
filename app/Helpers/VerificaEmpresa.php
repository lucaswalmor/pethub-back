<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\User;

class VerificaEmpresa
{
    /**
     * Verifica se uma empresa pertence ao usuário autenticado
     *
     * @param int $empresaId
     * @return bool
     */
    public static function verificaEmpresaPertenceAoUsuario(int $empresaId): bool
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return false;
        }
        
        return $usuario->empresas->contains('id', $empresaId);
    }

    /**
     * Verifica se dois usuários pertencem à mesma empresa
     *
     * @param int $usuarioId
     * @return bool
     */
    public static function verificaUsuariosMesmaEmpresa(int $usuarioId): bool
    {
        $usuarioAutenticado = Auth::user();
        if (!$usuarioAutenticado) {
            return false;
        }

        // Carregar empresas do usuário autenticado
        $usuarioAutenticado = User::with('empresas')->find($usuarioAutenticado->id);
        if (!$usuarioAutenticado) {
            return false;
        }

        $usuario = User::with('empresas')->find($usuarioId);
        
        if (!$usuario) {
            return false;
        }

        $empresasAutenticado = $usuarioAutenticado->empresas->pluck('id');
        $empresasUsuario = $usuario->empresas->pluck('id');
        
        // Verifica se há interseção entre as empresas (pertencem à mesma empresa)
        return $empresasAutenticado->intersect($empresasUsuario)->isNotEmpty();
    }

    /**
     * Obtém todas as empresas do usuário autenticado
     *
     * @return Collection
     */
    public static function obterEmpresasDoUsuario(): Collection
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return collect();
        }
        // A relação empresas retorna uma Collection quando acessada
        return $usuario->empresas;
    }
}
