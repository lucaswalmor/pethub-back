<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\FormatHelper;

class UberlandiaBairrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bairros = [
            // REGIÃO CENTRAL
            'Centro',
            'Fundinho',
            'Nossa Senhora Aparecida',
            'Osvaldo Rezende',
            'Brasil',
            'Tabajaras',
            'Lídice',
            'Martins',
            'Cazeca',
            'Bom Jesus',
            'Daniel Fonseca',
            'Nossa Senhora das Graças',
            'São José',

            // ZONA NORTE
            'Jardim das Palmeiras',
            'Luizote de Freitas',
            'Jardim Patrícia',
            'Jardim Holanda',
            'Jardim Europa',
            'Jardim Canaã',
            'Mansour',
            'Dona Zulmira',
            'Taiaman',
            'Guarani',
            'Tocantins',
            'Morada do Sol',
            'Monte Hebron',
            'Residencial Pequis',
            'Morada Nova',

            // ZONA OESTE
            'Jaraguá',
            'Planalto',
            'Chácaras Tubalina',
            'Chácaras Panorama',

            // ZONA SUL
            'Patrimônio',
            'Copacabana',
            'Marta Helena',
            'Shopping Park',
            'Granada',
            'Cidade Jardim',
            'Presidente Roosevelt',
            'Pacaembu',
            'Vigilato Pereira',
            'São Jorge',
            'Jardim Karaíba',
            'Tubalina',
            'Morada da Colina',
            'Laranjeiras',
            'Jardim Botânico',

            // ZONA LESTE
            'Santa Mônica',
            'Tibery',
            'Segismundo Pereira',
            'Umuarama',
            'Alto Umuarama',
            'Custódio Pereira',
            'Aclimação',
            'Mansões Aeroporto',
            'Alvorada',
            'Novo Mundo',
            'Morumbi',
            'Residencial Integração',
            'Morada dos Pássaros',
            'Jardim Ipanema',
            'Portal do Vale',
            'Granja Marileusa',
            'Grand Ville',

            // DISTRITOS
            'Cruzeiro dos Peixotos',
            'Martinésia',
            'Tapuirama',
            'Miraporanga',
            'Miranda',

            // OUTROS BAIRROS
            'Santa Luzia',
            'Esperança',
            'Saraiva',
            'Carajás',
            'Cruzeiro do Sul',
        ];

        $timestamp = now();

        $dados = array_map(function($bairro) use ($timestamp) {
            return [
                'nome' => $bairro,
                'slug' => FormatHelper::formatSlug($bairro),
                'cidade' => 'Uberlândia',
                'estado' => 'MG',
                'ativo' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $bairros);

        // Inserir em lotes para melhor performance
        foreach (array_chunk($dados, 50) as $chunk) {
            DB::table('bairros')->insert($chunk);
        }

        $this->command->info('✓ ' . count($bairros) . ' bairros de Uberlândia-MG inseridos com sucesso!');
    }
}