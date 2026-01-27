<?php

namespace App\Http\Controllers;

use App\Models\Permissao;
use Illuminate\Http\Request;

class PermissaoController extends Controller
{
    /**
     * Display a listing of all permissions.
     */
    public function index()
    {
        $permissoes = Permissao::where('ativo', true)
            ->get(['id', 'nome', 'slug']);

        return response()->json([
            'permissoes' => $permissoes
        ]);
    }
}