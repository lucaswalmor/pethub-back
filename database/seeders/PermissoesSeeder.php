<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        // Array de permissões - adicionar novas aqui conforme necessário
        $permissoes = [
            // Sistema
            ['nome' => 'Acesso Total', 'slug' => 'sistema.acesso_total'],

            // Usuários
            ['nome' => 'Listar Usuários', 'slug' => 'usuarios.listar'],
            ['nome' => 'Criar Usuários', 'slug' => 'usuarios.criar'],
            ['nome' => 'Editar Usuários', 'slug' => 'usuarios.editar'],
            ['nome' => 'Deletar Usuários', 'slug' => 'usuarios.deletar'],
            ['nome' => 'Gerenciar Permissões', 'slug' => 'usuarios.gerenciar_permissoes'],

            // Empresas
            ['nome' => 'Listar Empresas', 'slug' => 'empresas.listar'],
            ['nome' => 'Criar Empresas', 'slug' => 'empresas.criar'],
            ['nome' => 'Editar Empresas', 'slug' => 'empresas.editar'],
            ['nome' => 'Deletar Empresas', 'slug' => 'empresas.deletar'],
            ['nome' => 'Gerenciar Funcionários', 'slug' => 'empresas.gerenciar_funcionarios'],
            ['nome' => 'Upload de Imagens', 'slug' => 'empresas.upload_imagens'],
        ];

        foreach ($permissoes as $permissao) {
            // Verifica se a permissão já existe
            $existe = DB::table('permissoes')->where('slug', $permissao['slug'])->exists();

            if (!$existe) {
                DB::table('permissoes')->insert([
                    'nome' => $permissao['nome'],
                    'slug' => $permissao['slug'],
                    'ativo' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $this->command->info("Permissão criada: {$permissao['nome']}");
            } else {
                $this->command->info("Permissão já existe: {$permissao['nome']}");
            }
        }
    }
}