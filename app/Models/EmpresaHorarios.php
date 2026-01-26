<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaHorarios extends Model
{
    use HasFactory;

    protected $table = 'empresa_horarios';

    protected $fillable = [
        'empresa_id',
        'dia_semana',
        'slug',
        'horario_inicio',
        'horario_fim',
        'padrao',
    ];

    // Relação com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
