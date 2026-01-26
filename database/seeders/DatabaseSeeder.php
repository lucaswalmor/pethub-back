<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executar seeders do sistema
        $this->call([
            SistemaSeeder::class,
        ]);

        // User::factory(10)->create();

        // Criar usuário de teste (remover em produção)
        User::create([
            'permissao_id' => 1, // admin
            'nome' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'telefone' => '(11) 99999-9999',
            'ativo' => true,
        ]);
    }
}
