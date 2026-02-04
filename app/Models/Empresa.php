<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'tipo_pessoa',
        'razao_social',
        'nome_fantasia',
        'slug',
        'email',
        'telefone',
        'cpf_cnpj',
        'path_logo',
        'path_banner',
        'nicho_id',
        'cadastro_completo',
        'ativo',
    ];

    // Relação com nicho
    public function nicho()
    {
        return $this->belongsTo(NichosEmpresa::class, 'nicho_id');
    }

    // Relação com endereço
    public function endereco()
    {
        return $this->hasOne(EmpresaEndereco::class, 'empresa_id');
    }

    // Relação com configurações
    public function configuracoes()
    {
        return $this->hasOne(EmpresaConfiguracoes::class, 'empresa_id');
    }

    // Relação com horários
    public function horarios()
    {
        return $this->hasMany(EmpresaHorarios::class, 'empresa_id');
    }

    // Relação com bairros de entrega
    public function bairrosEntregas()
    {
        return $this->hasMany(EmpresaBairrosEntregas::class, 'empresa_id');
    }

    // Relação com assinatura
    public function assinatura()
    {
        return $this->hasOne(EmpresaAssinatura::class, 'empresa_id');
    }

    // Relação com formas de pagamento
    public function formasPagamentos()
    {
        return $this->hasMany(EmpresaFormasPagamentos::class, 'empresa_id');
    }

    // Relação com usuários
    public function usuarios()
    {
        return $this->hasMany(UsuarioEmpresas::class, 'empresa_id');
    }

    // Relação com favoritos
    public function empresaFavoritos()
    {
        return $this->hasMany(EmpresaFavorito::class, 'empresa_id');
    }

    // Relação com produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'empresa_id');
    }

    // Relação com pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'empresa_id');
    }

    // Relação com avaliações
    public function avaliacoes()
    {
        return $this->hasMany(EmpresaAvaliacao::class, 'empresa_id');
    }

    // Relação com cupons da empresa
    public function cupons()
    {
        return $this->hasMany(EmpresaCupom::class, 'empresa_id');
    }

    /**
     * Calcular média das avaliações da empresa
     */
    public function calcularMediaAvaliacoes()
    {
        return $this->avaliacoes()->selectRaw('AVG(nota) as media, COUNT(*) as total')->first();
    }

    /**
     * Obter cupons ativos da empresa
     */
    public function cuponsAtivos()
    {
        return $this->cupons()->ativos()->get();
    }

    /**
     * Verificar se a empresa está aberta no momento
     */
    public function isAberta(): bool
    {
        if (!$this->relationLoaded('horarios')) {
            return false;
        }

        $agora = Carbon::now('America/Sao_Paulo');
        $diaSemanaIngles = strtolower($agora->format('l'));

        $mapaDias = [
            'monday' => 'segunda',
            'tuesday' => 'terca',
            'wednesday' => 'quarta',
            'thursday' => 'quinta',
            'friday' => 'sexta',
            'saturday' => 'sabado',
            'sunday' => 'domingo',
        ];

        $diaSemana = $mapaDias[$diaSemanaIngles] ?? null;
        $horaAtual = $agora->format('H:i:s');

        if (!$diaSemana) {
            return false;
        }

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
