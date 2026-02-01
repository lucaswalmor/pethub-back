<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SistemaCupom;
use App\Models\UsuarioCupom;
use Carbon\Carbon;

class CupomBoasVindasSeeder extends Seeder
{
    /**
     * Criar cupom de boas-vindas para todos os clientes existentes
     *
     * Este seeder cria:
     * 1. Um cupom do sistema com desconto
     * 2. Atribui esse cupom a todos os usuários clientes existentes
     */
    public function run(): void
    {
        // Configurações do cupom
        $cupomConfig = [
            'codigo' => 'BEMVINDO10',
            'tipo' => 'percentual', // 'percentual' ou 'fixo'
            'valor' => 10, // 10% de desconto
            'data_inicio' => Carbon::now(),
            'data_fim' => Carbon::now()->addMonths(3), // Válido por 3 meses
            'limite_uso_total' => 1000, // Limite total de usos
            'ativo' => true,
        ];

        // Criar o cupom do sistema
        $cupom = SistemaCupom::firstOrCreate(
            ['codigo' => $cupomConfig['codigo']],
            [
                'tipo' => $cupomConfig['tipo'],
                'valor' => $cupomConfig['valor'],
                'data_inicio' => $cupomConfig['data_inicio'],
                'data_fim' => $cupomConfig['data_fim'],
                'limite_uso_total' => $cupomConfig['limite_uso_total'],
                'ativo' => $cupomConfig['ativo'],
            ]
        );

        $this->command->info("✓ Cupom '{$cupom->codigo}' criado/encontrado no sistema");

        // Buscar todos os usuários clientes (não administradores e não lojistas)
        // Clientes são usuários que:
        // - NÃO são is_master (não são admin)
        // - NÃO têm empresas associadas (não são lojistas)
        $usuarios = User::where(function ($query) {
                $query->where('is_master', false)
                      ->orWhereNull('is_master');
            })
            ->whereDoesntHave('empresas')
            ->get();

        if ($usuarios->isEmpty()) {
            $this->command->warn('⚠ Nenhum cliente encontrado no banco de dados');
            $this->command->info('💡 Dica: Clientes são usuários que não são administradores e não possuem empresas');
            return;
        }

        $this->command->info("→ Encontrados {$usuarios->count()} clientes");

        $cuponsAtribuidos = 0;
        $cuponsExistentes = 0;

        // Atribuir o cupom a cada usuário
        foreach ($usuarios as $usuario) {
            // Verificar se o usuário já possui este cupom
            $cupomExistente = UsuarioCupom::where('usuario_id', $usuario->id)
                ->where('sistema_cupom_id', $cupom->id)
                ->exists();

            if ($cupomExistente) {
                $cuponsExistentes++;
                $this->command->line("  • {$usuario->nome} já possui o cupom");
                continue;
            }

            // Criar a relação usuário-cupom
            UsuarioCupom::create([
                'usuario_id' => $usuario->id,
                'sistema_cupom_id' => $cupom->id,
                'usado_em' => null,
                'pedido_id' => null,
            ]);

            $cuponsAtribuidos++;
            $this->command->line("  ✓ Cupom atribuído para: {$usuario->nome} ({$usuario->email})");
        }

        // Resumo final
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('          RESUMO DA OPERAÇÃO            ');
        $this->command->info('═══════════════════════════════════════');
        $this->command->table(
            ['Métrica', 'Valor'],
            [
                ['Código do Cupom', $cupom->codigo],
                ['Tipo de Desconto', $cupom->tipo === 'percentual' ? "{$cupom->valor}%" : "R$ {$cupom->valor}"],
                ['Validade', $cupom->data_fim->format('d/m/Y')],
                ['Clientes Encontrados', $usuarios->count()],
                ['Cupons Atribuídos', $cuponsAtribuidos],
                ['Cupons Já Existentes', $cuponsExistentes],
                ['Total Processado', $cuponsAtribuidos + $cuponsExistentes],
            ]
        );
        $this->command->info('═══════════════════════════════════════');
        $this->command->newLine();

        if ($cuponsAtribuidos > 0) {
            $this->command->info("✓ Seeder executada com sucesso!");
            $this->command->info("→ {$cuponsAtribuidos} cliente(s) receberam o cupom '{$cupom->codigo}'");
        } else {
            $this->command->warn("⚠ Nenhum cupom novo foi atribuído (todos os clientes já possuíam o cupom)");
        }
    }
}
