<?php

namespace App\Services\V1;

/**
 * Class CombinationGenerator
 *
 * Servicio encargado de la generación de las combinaciones aleatorias de 4 dígitos todos diferentes que
 * se usan en los juegos
 *
 * @package App\Services\V1
 */
class CombinationGenerator
{
    /** Genera una combinació aleatoria de 4 dígitos todos diferentes
     *
     * @return string Combinación de 4 dígitos generada
     */
    public function generate(): string
    {
        $chars = "0123456789";

        return substr(str_shuffle( $chars), 0, 4);
    }
}
