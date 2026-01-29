<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SiteEmpresaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dados = [
            'id' => $this->id,
            'razao_social' => $this->razao_social,
            'nome_fantasia' => $this->nome_fantasia,
            'slug' => $this->slug,
            'path_logo' => $this->path_logo ? $this->path_logo : null,
            'path_banner' => $this->path_banner ? $this->path_banner : null,
            // 'path_logo' => $this->path_logo ? asset('storage/' . $this->path_logo) : null,
            // 'path_banner' => $this->path_banner ? asset('storage/' . $this->path_banner) : null,
            'ativo' => $this->ativo,
            'empresa_aberta' => $this->isAberta(),
            'empresa_nova' => $this->created_at >= now()->subMonth(),
        ];

        // Média de avaliações
        if ($this->relationLoaded('avaliacoes')) {
            $media = $this->avaliacoes()->selectRaw('AVG(nota) as media, COUNT(*) as total')->first();
            $dados['nota_media'] = $media ? round($media->media, 1) : 0;
            $dados['total_avaliacoes'] = $media ? $media->total : 0;
        }

        // Nicho
        if ($this->relationLoaded('nicho')) {
            $dados['nicho'] = [
                'id' => $this->nicho->id,
                'nome' => $this->nicho->nome,
                'imagem' => $this->nicho->imagem ? $this->nicho->imagem : null,
                'slug' => $this->nicho->slug,
            ];
        }

        // Informações públicas da empresa para clientes
        $dados['endereco'] = $this->whenLoaded('endereco', function () {
            return [
                'logradouro' => $this->endereco->logradouro,
                'numero' => $this->endereco->numero,
                'bairro' => $this->endereco->bairro,
                'cidade' => $this->endereco->cidade,
                'estado' => $this->endereco->estado,
                'cep' => $this->endereco->cep,
            ];
        });

        $dados['horarios'] = $this->whenLoaded('horarios', function () {
            return $this->horarios->map(function ($horario) {
                return [
                    'dia_semana' => $horario->dia_semana,
                    'horario_inicio' => $horario->horario_inicio,
                    'horario_fim' => $horario->horario_fim,
                ];
            });
        });

        $dados['bairros_entrega'] = $this->whenLoaded('bairrosEntregas', function () {
            return $this->bairrosEntregas->map(function ($bairro) {
                return [
                    'id' => $bairro->id,
                    'nome' => $bairro->bairro->nome,
                    'valor_entrega' => $bairro->valor_entrega,
                    'valor_entrega_minimo' => $bairro->valor_entrega_minimo,
                ];
            });
        });

        $dados['formas_pagamento'] = $this->whenLoaded('formasPagamentos', function () {
            return $this->formasPagamentos->map(function ($forma) {
                return [
                    'id' => $forma->forma_pagamento_id,
                    'nome' => $forma->formaPagamento->nome,
                    'slug' => $forma->formaPagamento->slug,
                ];
            });
        });

        $dados['configuracoes'] = $this->whenLoaded('configuracoes', function () {
            return [
                'faz_entrega' => $this->configuracoes->faz_entrega,
                'faz_retirada' => $this->configuracoes->faz_retirada,
                'valor_entrega_padrao' => $this->configuracoes->valor_entrega_padrao,
            ];
        });

        $dados['produtos'] = $this->whenLoaded('produtos', function () {
            $produtosAtivos = $this->produtos->where('ativo', true);

            // Agrupar produtos por categoria
            $produtosPorCategoria = $produtosAtivos->groupBy(function ($produto) {
                return $produto->categoria ? $produto->categoria->nome : 'Sem Categoria';
            })->map(function ($produtos, $categoriaNome) {
                return [
                    'categoria' => $categoriaNome,
                    'produtos' => \App\Http\Resources\Produto\ProdutoResource::collection($produtos)
                ];
            })->values();

            return $produtosPorCategoria;
        });

        $dados['avaliacoes_recentes'] = $this->whenLoaded('avaliacoes', function () {
            return $this->avaliacoes->map(function ($avaliacao) {
                return [
                    'id' => $avaliacao->id,
                    'nota' => $avaliacao->nota,
                    'comentario' => $avaliacao->comentario,
                    'created_at' => $avaliacao->created_at,
                    'usuario' => [
                        'nome' => $avaliacao->usuario->nome,
                    ]
                ];
            });
        });

        return $dados;
    }

    /**
     * Lógica para verificar se a empresa está aberta
     */
    private function isAberta(): bool
    {
        if (!$this->relationLoaded('horarios')) {
            return false;
        }

        $agora = Carbon::now('America/Sao_Paulo');
        $diaSemanaIngles = strtolower($agora->format('l'));
        
        $mapaDias = [
            'monday' => 'segunda',
            'tuesday' => 'terça',
            'wednesday' => 'quarta',
            'thursday' => 'quinta',
            'friday' => 'sexta',
            'saturday' => 'sábado',
            'sunday' => 'domingo',
        ];

        $diaSemana = $mapaDias[$diaSemanaIngles];
        $horaAtual = $agora->format('H:i:s');

        foreach ($this->horarios as $horario) {
            if ($horario->dia_semana === $diaSemana) {
                if ($horaAtual >= $horario->horario_inicio && $horaAtual <= $horario->horario_fim) {
                    return true;
                }
            }
        }

        return false;
    }
}