<?php

namespace App\Http\Resources\Usuario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'ativo' => $this->ativo,
            'is_master' => $this->is_master,
            'email_verified_at' => $this->email_verified_at,

            // Relacionamentos
            'enderecos' => $this->whenLoaded('enderecos', function () {
                return $this->enderecos->map(function ($endereco) {
                    return [
                        'id' => $endereco->id,
                        'cep' => $endereco->cep,
                        'rua' => $endereco->rua,
                        'numero' => $endereco->numero,
                        'complemento' => $endereco->complemento,
                        'bairro' => $endereco->bairro,
                        'cidade' => $endereco->cidade,
                        'estado' => $endereco->estado,
                        'ponto_referencia' => $endereco->ponto_referencia,
                        'observacoes' => $endereco->observacoes,
                        'ativo' => $endereco->ativo,
                        'endereco_padrao' => $endereco->endereco_padrao,
                    ];
                });
            }),

            'empresas' => $this->whenLoaded('empresas', function () {
                return $this->empresas->map(function ($empresa) {
                    return [
                        'id' => $empresa->id,
                        'razao_social' => $empresa->razao_social,
                        'nome_fantasia' => $empresa->nome_fantasia,
                        'slug' => $empresa->slug,
                        'email' => $empresa->email,
                        'telefone' => $empresa->telefone,
                        'cnpj' => $empresa->cnpj,
                        'cadastro_completo' => $empresa->cadastro_completo,
                        'ativo' => $empresa->ativo,
                    ];
                });
            }),

            'permissoes' => $this->whenLoaded('permissoes', function () {
                return $this->permissoes->map(function ($permissao) {
                    return [
                        'id' => $permissao->id,
                        'nome' => $permissao->nome,
                        'slug' => $permissao->slug,
                        'ativo' => $permissao->ativo,
                    ];
                });
            }),
        ];
    }
}