<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categorias extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    // Relação com produtos
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'categoria_id');
    }
}
