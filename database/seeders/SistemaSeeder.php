<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        // -----------------------------
        // Categorias
        // -----------------------------
        $categorias = [
            'Rações',
            'Brinquedos',
            'Acessórios',
            'Higiene e Limpeza',
            'Medicamentos',
            'Petiscos',
            'Serviços',
            'Outros',
        ];

        foreach ($categorias as $categoria) {
            DB::table('categorias')->insert([
                'nome' => $categoria,
                'slug' => strtolower(str_replace(' ', '-', $categoria)),
                'ativo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Status de pedidos
        // -----------------------------
        $statusPedidos = [
            'pendente',
            'confirmado',
            'em_preparacao',
            'em_entrega',
            'entregue',
            'cancelado',
        ];

        foreach ($statusPedidos as $status) {
            DB::table('status_pedidos')->insert([
                'nome' => ucfirst(str_replace('_', ' ', $status)),
                'slug' => $status,
                'ativo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Nicho da empresa
        // -----------------------------
        $nichos = [
            [
                'nome' => 'Petshop',
                'imagem' => 'https://i.ibb.co/fzbzY9WQ/petshop.png'
            ],
            [
                'nome' => 'Agropecuária',
                'imagem' => 'https://i.ibb.co/GG6QS07/agropecu-ria.png'
            ],
            [
                'nome' => 'Banho e Tosa',
                'imagem' => 'https://i.ibb.co/R4jrzbRf/banho-e-tosa.png'
            ],
            [
                'nome' => 'Veterinária',
                'imagem' => 'https://i.ibb.co/nsbkjDHt/veterin-ria.png'
            ],
            [
                'nome' => 'Caça e Pesca',
                'imagem' => 'https://i.ibb.co/p605fLGn/cacaepesca.png'
            ],
        ];

        foreach ($nichos as $nicho) {
            DB::table('nichos_empresa')->insert([
                'nome' => $nicho['nome'],
                'slug' => strtolower(str_replace(' ', '-', $nicho['nome'])),
                'imagem' => $nicho['imagem'],
                'ativo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Formas de pagamento
        // -----------------------------
        $formasPagamentos = [
            'Dinheiro',
            'Cartão de Crédito',
            'Cartão de Débito',
            'PIX',
            'Transferência Bancária',
        ];

        foreach ($formasPagamentos as $fp) {
            DB::table('formas_pagamentos')->insert([
                'nome' => $fp,
                'slug' => strtolower(str_replace(' ', '-', $fp)),
                'ativo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Unidades de medida
        // -----------------------------
        $unidades = [
            ['nome' => 'Unidade', 'sigla' => 'Un'],
            ['nome' => 'Pacote', 'sigla' => 'Pct'],
            ['nome' => 'Quilo', 'sigla' => 'KG'],
            ['nome' => 'Litro', 'sigla' => 'L'],
            ['nome' => 'Grama', 'sigla' => 'g'],
        ];

        foreach ($unidades as $u) {
            DB::table('unidades_medidas')->insert([
                'nome' => $u['nome'],
                'sigla' => $u['sigla'],
                'ativo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        // -----------------------------
        // Permissões (executa seeder dedicada)
        // -----------------------------
        $this->call(PermissoesSeeder::class);

        // -----------------------------
        // Bairros de Uberlândia-MG
        // -----------------------------
        $this->call(UberlandiaBairrosSeeder::class);

        $this->command->info('✓ Seeder inicial do sistema executado com sucesso!');
    }
}
