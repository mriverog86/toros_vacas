<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Game
 *
 * Entidad que almacena los datos de un juego
 *
 * @package App\Models
 */
class Game extends Model
{
    use HasFactory;

    /**
     * Los atributos que pueden ser asignados a la vez en una operación.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'age',
        'combination',
        'won',
        'score'
    ];
}
