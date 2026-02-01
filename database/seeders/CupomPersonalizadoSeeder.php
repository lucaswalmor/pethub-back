<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SistemaCupom;
use App\Models\UsuarioCupom;
use Carbon\Carbon;

class CupomPersonalizadoSeeder extends Seeder
{
    /**
     * Criar cupom personalizado para todos os clientes
     * 
     * Você pode personalizar as configurações do cupom editando o array $cupomConfig
     * ou passar parâmetros via command line (future implementation)
     */
    public function run(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // CONFIGURAÇÃO DO CUPOM - EDITE AQUI PARA PERSONALIZAR
        // ═══════════════════════════════════════════════════════════════
        
        $cupomConfig = [
            // Código único do cupom (ex: BEMVINDO10, NATAL2026, PRIMEIRACOMPRA)
            'codigo' => 'DESCONTO15',
            
            // Tipo de desconto: 'percentual' ou 'fixo'
            'tipo' => 'percentual',
            
            // Valor do desconto:
            // - Se tipo='percentual': valor de 0 a 100 (ex: 10 = 10%)
            // - Se tipo='fixo': valor em reais (ex: 20 = R$ 20,00)
            'valor' => 15,
            
            // Data de início (quando o cupom fica ativo)
            'data_inicio' => Carbon::now(),
            
            // Data de fim (quando o cupom expira)
            // Exemplos:
            // - addDays(7): 7 dias
            // - addWeeks(2): 2 semanas
            // - addMonths(3): 3 meses
            // - addYear(): 1 ano
            'data_fim' => Carbon::now()->addMonths(6),
            
            // Limite total de usos do cupom (quantas vezes pode ser usado no total)
            'limite_uso_total' => 500,
            
            // Cupom ativo?
            'ativo' => true,
        ];

        // ═══════════════════════════════════════════════════════════════
        // PROCESSAMENTO - NÃO É NECESSÁRIO EDITAR ABAIXO
        // ═══════════════════════════════════════════════════════════════

        $this->command->info('═══════════════════════════════════════');
        $this->command->info('   CRIAÇÃO DE CUPOM PERSONALIZADO       ');
        $this->command->info('═══════════════════════════════════════');
        $this->command->newLine();

        // Validar configurações
        if (!in_array($cupomConfig['tipo'], ['percentual', 'fixo'])) {
            $this->command->error('✗ Erro: O tipo deve ser "percentual" ou "fixo"');
            return;
        }

        if ($cupomConfig['tipo'] === 'percentual' && ($cupomConfig['valor'] < 0 || $cupomConfig['valor'] > 100)) {
            $this->command->error('✗ Erro: Para desconto percentual, o valor deve estar entre 0 e 100');
            return;
        }

        if ($cupomConfig['valor'] <= 0) {
            $this->command->error('✗ Erro: O valor do desconto deve ser maior que zero');
            return;
        }

        // Criar o cupom do sistema
        $cupomExistente = SistemaCupom::where('codigo', $cupomConfig['codigo'])->first();
        
        if ($cupomExistente) {
            $this->command->warn("⚠ Cupom '{$cupomConfig['codigo']}' já existe no sistema!");
            $this->command->ask('Deseja continuar e atribuir este cupom aos usuários? (digite qualquer coisa para continuar ou Ctrl+C para cancelar)');
            $cupom = $cupomExistente;
        } else {
            $cupom = SistemaCupom::create([
                'codigo' => $cupomConfig['codigo'],
                'tipo' => $cupomConfig['tipo'],
                'valor' => $cupomConfig['valor'],
                'data_inicio' => $cupomConfig['data_inicio'],
                'data_fim' => $cupomConfig['data_fim'],
                'limite_uso_total' => $cupomConfig['limite_uso_total'],
                'ativo' => $cupomConfig['ativo'],
            ]);
            $this->command->info("✓ Cupom '{$cupom->codigo}' criado com sucesso!");
        }

        // Exibir detalhes do cupom
        $this->command->newLine();
        $this->command->info('📋 DETALHES DO CUPOM:');
        $this->command->table(
            ['Campo', 'Valor'],
            [
                ['Código', $cupom->codigo],
                ['Tipo', ucfirst($cupom->tipo)],
                ['Desconto', $cupom->tipo === 'percentual' ? "{$cupom->valor}%" : "R$ " . number_format($cupom->valor, 2, ',', '.')],
                ['Data Início', $cupom->data_inicio->format('d/m/Y H:i')],
                ['Data Fim', $cupom->data_fim->format('d/m/Y H:i')],
                ['Dias até expirar', $cupom->data_fim->diffInDays(Carbon::now())],
                ['Limite de Usos', $cupom->limite_uso_total],
                ['Status', $cupom->ativo ? '✓ Ativo' : '✗ Inativo'],
            ]
        );

        // Buscar clientes (excluindo admins e lojistas)
        $this->command->newLine();
        $this->command->info('🔍 Buscando clientes...');
        
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
            $this->command->newLine();
            $this->command->info('Você pode criar clientes através de:');
            $this->command->line('  1. Registro no frontend (/register)');
            $this->command->line('  2. Seeder de usuários (se houver)');
            $this->command->line('  3. Manualmente no banco de dados');
            return;
        }

        $this->command->info("✓ Encontrados {$usuarios->count()} clientes no sistema");
        $this->command->newLine();

        // Confirmar antes de atribuir
        $this->command->warn("⚠ Você está prestes a atribuir o cupom '{$cupom->codigo}' para {$usuarios->count()} cliente(s)");
        $this->command->ask('Digite qualquer coisa para continuar ou Ctrl+C para cancelar');

        // Processar atribuição
        $this->command->newLine();
        $this->command->info('🔄 Processando atribuições...');
        $this->command->newLine();

        $cuponsAtribuidos = 0;
        $cuponsExistentes = 0;
        $erros = 0;

        $progressBar = $this->command->getOutput()->createProgressBar($usuarios->count());
        $progressBar->start();

        foreach ($usuarios as $usuario) {
            try {
                // Verificar se já existe
                $cupomExistente = UsuarioCupom::where('usuario_id', $usuario->id)
                    ->where('sistema_cupom_id', $cupom->id)
                    ->exists();

                if ($cupomExistente) {
                    $cuponsExistentes++;
                } else {
                    // Criar a relação
                    UsuarioCupom::create([
                        'usuario_id' => $usuario->id,
                        'sistema_cupom_id' => $cupom->id,
                        'usado_em' => null,
                        'pedido_id' => null,
                    ]);
                    $cuponsAtribuidos++;
                }
            } catch (\Exception $e) {
                $erros++;
                $this->command->error("Erro ao atribuir cupom para {$usuario->email}: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine(2);

        // Resumo final
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('          RESUMO FINAL                  ');
        $this->command->info('═══════════════════════════════════════');
        
        $this->command->table(
            ['Métrica', 'Quantidade', 'Percentual'],
            [
                ['Total de Clientes', $usuarios->count(), '100%'],
                ['Cupons Atribuídos (Novos)', $cuponsAtribuidos, round(($cuponsAtribuidos / $usuarios->count()) * 100, 1) . '%'],
                ['Cupons Já Existentes', $cuponsExistentes, round(($cuponsExistentes / $usuarios->count()) * 100, 1) . '%'],
                ['Erros', $erros, $erros > 0 ? round(($erros / $usuarios->count()) * 100, 1) . '%' : '0%'],
            ]
        );

        $this->command->info('═══════════════════════════════════════');
        $this->command->newLine();

        // Mensagem final
        if ($erros > 0) {
            $this->command->error("✗ Seeder finalizada com {$erros} erro(s)");
        } elseif ($cuponsAtribuidos > 0) {
            $this->command->info("✓ Seeder executada com sucesso!");
            $this->command->info("→ {$cuponsAtribuidos} cliente(s) receberam o cupom '{$cupom->codigo}'");
            
            if ($cuponsExistentes > 0) {
                $this->command->line("→ {$cuponsExistentes} cliente(s) já possuíam o cupom");
            }
        } else {
            $this->command->warn("⚠ Nenhum cupom novo foi atribuído");
            $this->command->line("→ Todos os {$cuponsExistentes} clientes já possuíam o cupom");
        }

        $this->command->newLine();
        $this->command->info("💡 Os clientes podem ver seus cupons em: Meu Perfil → Meus Cupons");
    }
}
