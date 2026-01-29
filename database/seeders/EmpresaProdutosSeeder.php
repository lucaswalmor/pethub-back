<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmpresaProdutosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        // -----------------------------
        // Criar 5 empresas com nichos diferentes
        // -----------------------------
        $nichosEmpresas = [
            1 => ['nome' => 'PetShop Central', 'nicho_id' => 1, 'nicho_nome' => 'Petshop'],
            2 => ['nome' => 'Agropecuária São João', 'nicho_id' => 2, 'nicho_nome' => 'Agropecuária'],
            3 => ['nome' => 'Banho & Tosa Premium', 'nicho_id' => 3, 'nicho_nome' => 'Banho e Tosa'],
            4 => ['nome' => 'Veterinária Vida Animal', 'nicho_id' => 4, 'nicho_nome' => 'Veterinária'],
            5 => ['nome' => 'Casa de Pesca e Caça', 'nicho_id' => 5, 'nicho_nome' => 'Caça e Pesca'],
        ];

        foreach ($nichosEmpresas as $index => $dadosEmpresa) {
            $razaoSocial = $dadosEmpresa['nome'] . ' LTDA';

            $empresaId = DB::table('empresas')->insertGetId([
                'razao_social' => $razaoSocial,
                'nome_fantasia' => $dadosEmpresa['nome'],
                'slug' => Str::slug($dadosEmpresa['nome']),
                'email' => 'contato@' . Str::slug($dadosEmpresa['nome']) . '.com',
                'telefone' => '(34) 9' . str_pad($index, 4, '0', STR_PAD_LEFT) . '-0000',
                'cnpj' => '12.345.678/000' . $index . '-' . str_pad($index * 10, 2, '0', STR_PAD_LEFT),
                'nicho_id' => $dadosEmpresa['nicho_id'],
                'cadastro_completo' => true,
                'ativo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $this->command->info('✓ Empresa "' . $dadosEmpresa['nome'] . '" (' . $dadosEmpresa['nicho_nome'] . ') criada com ID: ' . $empresaId);

            // Criar dados relacionados para cada empresa
            $this->criarDadosEmpresa($empresaId, $index, $dadosEmpresa['nicho_id'], $timestamp);
        }

        $this->command->info('✓ 5 empresas criadas com sucesso!');
    }

    /**
     * Criar dados relacionados para uma empresa específica
     */
    private function criarDadosEmpresa($empresaId, $empresaIndex, $nichoId, $timestamp)
    {
        $ruas = ['Rua das Flores', 'Avenida Brasil', 'Rua João Pessoa', 'Praça São Paulo', 'Rua Rio Branco'];
        $bairros = ['Centro', 'Fundinho', 'Jardim Europa', 'Morada Nova', 'Santa Mônica'];

        // -----------------------------
        // Criar endereço da empresa
        // -----------------------------
        DB::table('empresa_endereco')->insert([
            'empresa_id' => $empresaId,
            'logradouro' => $ruas[$empresaIndex - 1],
            'numero' => strval(100 + $empresaIndex * 50),
            'bairro' => $bairros[$empresaIndex - 1],
            'cidade' => 'Uberlândia',
            'estado' => 'MG',
            'cep' => '3840' . str_pad($empresaIndex, 2, '0', STR_PAD_LEFT) . '-000',
            'ponto_referencia' => null,
            'observacoes' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        // -----------------------------
        // Criar configurações da empresa
        // -----------------------------
        DB::table('empresa_configuracoes')->insert([
            'empresa_id' => $empresaId,
            'faz_entrega' => true,
            'faz_retirada' => true,
            'valor_entrega_padrao' => 10.00 + ($empresaIndex * 5.00),
            'telefone_comercial' => '(34) 9' . str_pad($empresaIndex, 4, '0', STR_PAD_LEFT) . '-0000',
            'instagram' => 'https://instagram.com/petshop' . $empresaIndex,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        // -----------------------------
        // Criar horários da empresa
        // -----------------------------
        $horarios = [
            ['dia_semana' => 'segunda', 'horario_inicio' => '08:00', 'horario_fim' => '18:00'],
            ['dia_semana' => 'terça', 'horario_inicio' => '08:00', 'horario_fim' => '18:00'],
            ['dia_semana' => 'quarta', 'horario_inicio' => '08:00', 'horario_fim' => '18:00'],
            ['dia_semana' => 'quinta', 'horario_inicio' => '08:00', 'horario_fim' => '18:00'],
            ['dia_semana' => 'sexta', 'horario_inicio' => '08:00', 'horario_fim' => '18:00'],
            ['dia_semana' => 'sábado', 'horario_inicio' => '08:00', 'horario_fim' => '12:00'],
        ];

        foreach ($horarios as $horario) {
            DB::table('empresa_horarios')->insert([
                'empresa_id' => $empresaId,
                'dia_semana' => $horario['dia_semana'],
                'slug' => Str::slug($horario['dia_semana']),
                'horario_inicio' => $horario['horario_inicio'],
                'horario_fim' => $horario['horario_fim'],
                'padrao' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Criar formas de pagamento da empresa
        // -----------------------------
        $formasPagamento = [
            ['forma_pagamento_id' => 1, 'ativo' => true], // Dinheiro
            ['forma_pagamento_id' => 2, 'ativo' => true], // Cartão de Crédito
            ['forma_pagamento_id' => 3, 'ativo' => true], // Cartão de Débito
            ['forma_pagamento_id' => 4, 'ativo' => true], // PIX
        ];

        foreach ($formasPagamento as $fp) {
            DB::table('empresa_formas_pagamentos')->insert([
                'empresa_id' => $empresaId,
                'forma_pagamento_id' => $fp['forma_pagamento_id'],
                'ativo' => $fp['ativo'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Criar bairros de entrega da empresa
        // -----------------------------
        $bairrosEntrega = [
            [
                'bairro_id' => $empresaIndex,
                'valor_entrega' => 5.00 + ($empresaIndex * 2.00),
                'valor_entrega_minimo' => 20.00 + ($empresaIndex * 5.00),
                'ativo' => true
            ],
            [
                'bairro_id' => $empresaIndex + 1,
                'valor_entrega' => 7.00 + ($empresaIndex * 2.00),
                'valor_entrega_minimo' => 25.00 + ($empresaIndex * 5.00),
                'ativo' => true
            ],
        ];

        foreach ($bairrosEntrega as $bairro) {
            DB::table('empresa_bairros_entregas')->insert([
                'empresa_id' => $empresaId,
                'bairro_id' => $bairro['bairro_id'],
                'valor_entrega' => $bairro['valor_entrega'],
                'valor_entrega_minimo' => $bairro['valor_entrega_minimo'],
                'ativo' => $bairro['ativo'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Criar produtos da empresa baseado no nicho
        // -----------------------------
        $produtosPorNicho = [
            1 => [ // Petshop
                [
                    'categoria_id' => 1, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Ração Premium para Cães Adultos', 'imagem' => '',
                    'descricao' => 'Ração premium de alta qualidade para cães adultos, rica em proteínas e vitaminas essenciais.',
                    'preco' => 89.90, 'estoque' => 50.0
                ],
                [
                    'categoria_id' => 1, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Ração Premium para Gatos', 'imagem' => '',
                    'descricao' => 'Ração premium especial para gatos, com fórmula balanceada para manter o pelo saudável.',
                    'preco' => 79.90, 'estoque' => 40.0
                ],
                [
                    'categoria_id' => 2, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Bola de Borracha para Cães', 'imagem' => '',
                    'descricao' => 'Bola de borracha resistente, ideal para brincadeiras e exercícios físicos.',
                    'preco' => 15.90, 'estoque' => 100.0
                ],
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Coleira Nylon para Cães', 'imagem' => '',
                    'descricao' => 'Coleira de nylon resistente e confortável, disponível em várias cores.',
                    'preco' => 29.90, 'estoque' => 75.0
                ],
                [
                    'categoria_id' => 4, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Shampoo para Cães e Gatos', 'imagem' => '',
                    'descricao' => 'Shampoo neutro suave para banho de cães e gatos, deixa o pelo macio.',
                    'preco' => 19.90, 'estoque' => 60.0
                ],
            ],
            2 => [ // Agropecuária
                [
                    'categoria_id' => 1, 'unidade_medida_id' => 3, 'tipo' => 'produto',
                    'nome' => 'Ração para Bovinos', 'imagem' => '',
                    'descricao' => 'Ração balanceada para bovinos de corte e leite, com alto teor proteico.',
                    'preco' => 45.00, 'estoque' => 200.0
                ],
                [
                    'categoria_id' => 1, 'unidade_medida_id' => 3, 'tipo' => 'produto',
                    'nome' => 'Ração para Aves', 'imagem' => '',
                    'descricao' => 'Ração completa para aves poedeiras e de corte.',
                    'preco' => 35.00, 'estoque' => 150.0
                ],
                [
                    'categoria_id' => 5, 'unidade_medida_id' => 2, 'tipo' => 'produto',
                    'nome' => 'Fertilizante Orgânico', 'imagem' => '',
                    'descricao' => 'Fertilizante orgânico natural para hortaliças e frutas.',
                    'preco' => 25.00, 'estoque' => 80.0
                ],
                [
                    'categoria_id' => 5, 'unidade_medida_id' => 2, 'tipo' => 'produto',
                    'nome' => 'Sementes de Milho', 'imagem' => '',
                    'descricao' => 'Sementes de milho híbrido de alta produtividade.',
                    'preco' => 15.00, 'estoque' => 500.0
                ],
                [
                    'categoria_id' => 5, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Inseticida Natural', 'imagem' => '',
                    'descricao' => 'Inseticida orgânico para controle de pragas em hortas.',
                    'preco' => 18.00, 'estoque' => 120.0
                ],
            ],
            3 => [ // Banho e Tosa
                [
                    'categoria_id' => 4, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Shampoo Profissional para Cães', 'imagem' => '',
                    'descricao' => 'Shampoo profissional para cães, remove sujeiras profundas.',
                    'preco' => 35.00, 'estoque' => 30.0
                ],
                [
                    'categoria_id' => 4, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Condicionador para Pelos', 'imagem' => '',
                    'descricao' => 'Condicionador especial para deixar o pelo brilhante e macio.',
                    'preco' => 28.00, 'estoque' => 25.0
                ],
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Tesoura Profissional para Tosa', 'imagem' => '',
                    'descricao' => 'Tesoura profissional para tosa de cães e gatos.',
                    'preco' => 45.00, 'estoque' => 15.0
                ],
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Perfume para Animais', 'imagem' => '',
                    'descricao' => 'Perfume especial para animais, deixa cheiro agradável por dias.',
                    'preco' => 22.00, 'estoque' => 40.0
                ],
                [
                    'categoria_id' => 4, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Cera para Patas', 'imagem' => '',
                    'descricao' => 'Cera hidratante especial para patas de cães.',
                    'preco' => 15.00, 'estoque' => 35.0
                ],
            ],
            4 => [ // Veterinária
                [
                    'categoria_id' => 5, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Antipulgas para Cães', 'imagem' => '',
                    'descricao' => 'Medicamento veterinário para controle de pulgas e carrapatos.',
                    'preco' => 45.00, 'estoque' => 20.0
                ],
                [
                    'categoria_id' => 5, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Vermífugo para Gatos', 'imagem' => '',
                    'descricao' => 'Vermífugo oral para gatos, elimina parasitas internos.',
                    'preco' => 25.00, 'estoque' => 30.0
                ],
                [
                    'categoria_id' => 5, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Vacina Antirrábica', 'imagem' => '',
                    'descricao' => 'Vacina antirrábica para cães e gatos, proteção anual.',
                    'preco' => 35.00, 'estoque' => 15.0
                ],
                [
                    'categoria_id' => 4, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Desinfetante Veterinário', 'imagem' => '',
                    'descricao' => 'Desinfetante específico para ambientes veterinários.',
                    'preco' => 18.00, 'estoque' => 25.0
                ],
                [
                    'categoria_id' => 5, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Anti-inflamatório para Animais', 'imagem' => '',
                    'descricao' => 'Anti-inflamatório veterinário para dores e inflamações.',
                    'preco' => 28.00, 'estoque' => 18.0
                ],
            ],
            5 => [ // Caça e Pesca
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Vara de Pesca Profissional', 'imagem' => '',
                    'descricao' => 'Vara de pesca telescópica de carbono, resistente e leve.',
                    'preco' => 120.00, 'estoque' => 12.0
                ],
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Molinete Shimano', 'imagem' => '',
                    'descricao' => 'Molinete profissional Shimano para pesca esportiva.',
                    'preco' => 85.00, 'estoque' => 8.0
                ],
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Escopeta de Caça', 'imagem' => '',
                    'descricao' => 'Escopeta calibre 12 para caça, com certificação.',
                    'preco' => 250.00, 'estoque' => 5.0
                ],
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Munição para Caça', 'imagem' => '',
                    'descricao' => 'Munição calibre 12 para caça de pequeno porte.',
                    'preco' => 15.00, 'estoque' => 100.0
                ],
                [
                    'categoria_id' => 3, 'unidade_medida_id' => 1, 'tipo' => 'produto',
                    'nome' => 'Isca Artificial para Pesca', 'imagem' => '',
                    'descricao' => 'Conjunto de iscas artificiais variadas para pesca.',
                    'preco' => 22.00, 'estoque' => 40.0
                ],
            ],
        ];

        $produtos = $produtosPorNicho[$nichoId] ?? $produtosPorNicho[1]; // fallback para petshop

        foreach ($produtos as $index => $produto) {
            $precoAjustado = $produto['preco'] + ($empresaIndex * 1.50);
            $estoqueAjustado = $produto['estoque'] + ($empresaIndex * 5);

            DB::table('produtos')->insert([
                'empresa_id' => $empresaId,
                'categoria_id' => $produto['categoria_id'],
                'unidade_medida_id' => $produto['unidade_medida_id'],
                'tipo' => $produto['tipo'],
                'nome' => $produto['nome'],
                'imagem' => $produto['imagem'],
                'slug' => Str::slug($produto['nome']),
                'descricao' => $produto['descricao'],
                'preco' => $precoAjustado,
                'estoque' => $estoqueAjustado,
                'destaque' => $index < 2, // Primeiros 2 produtos são destaques
                'ativo' => true,

                // Novas colunas
                'marca' => $this->getMarcaAleatoria(),
                'sku' => $index % 2 === 0 ? 'PROD-' . $empresaId . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) : null, // SKU apenas para produtos pares
                'preco_custo' => round($precoAjustado * 0.6, 2), // 60% do preço de venda
                'estoque_minimo' => round($estoqueAjustado * 0.1, 3), // 10% do estoque
                'peso' => $this->getPesoAleatorio($produto['categoria_id']),
                'altura' => $this->getDimensaoAleatoria(),
                'largura' => $this->getDimensaoAleatoria(),
                'comprimento' => $this->getDimensaoAleatoria(),
                'ordem' => $index,
                'preco_promocional' => $index % 3 === 0 ? round($precoAjustado * 0.8, 2) : null, // 20% desconto a cada 3 produtos
                'promocao_ate' => $index % 3 === 0 ? now()->addDays(rand(7, 30))->format('Y-m-d') : null,
                'tem_promocao' => $index % 3 === 0,

                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        $this->command->info('✓ Dados criados para a empresa ' . $empresaIndex . ' (endereço, configurações, horários, formas de pagamento, bairros de entrega e 5 produtos)!');
    }

    /**
     * Retorna uma marca aleatória
     */
    private function getMarcaAleatoria()
    {
        $marcas = [
            'Royal Canin',
            'Pedigree',
            'Whiskas',
            'Premier',
            'Golden',
            'Pro Plan',
            'Vital Farmina',
            'Nutrilus',
            'Biofresh',
            'GranPlus',
            'Fórmula Natural',
            'Pet Luxo',
            'MegaZoo',
            'ZooCenter',
            'Pet Shop Premium',
        ];

        return $marcas[array_rand($marcas)];
    }

    /**
     * Retorna um peso aleatório baseado na categoria
     */
    private function getPesoAleatorio($categoriaId)
    {
        $pesosPorCategoria = [
            1 => [1.0, 5.0, 15.0, 25.0], // Rações
            2 => [0.1, 0.3, 0.5, 1.0],   // Brinquedos
            3 => [0.2, 0.5, 1.0, 2.0],   // Acessórios
            4 => [0.3, 0.7, 1.2, 2.5],   // Higiene
            5 => [0.5, 1.5, 5.0, 25.0],  // Agropecuária
            6 => [0.1, 0.2, 0.5, 1.0],   // Medicamentos
            7 => [0.1, 0.3, 0.8, 2.0],   // Petiscos
        ];

        $pesos = $pesosPorCategoria[$categoriaId] ?? [0.1, 0.5, 1.0, 2.0];
        return $pesos[array_rand($pesos)];
    }

    /**
     * Retorna uma dimensão aleatória
     */
    private function getDimensaoAleatoria()
    {
        return rand(5, 50); // 5 a 50 cm
    }
}