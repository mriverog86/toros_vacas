<?php

namespace App\Services\V1;

/**
 * Class BullsCowsCalculator
 *
 * Servicio encargado de calcular la cantidad de toros y vacas dadas 2 cadenas de caracteres.
 *
 * @package App\Services\V1
 */
class BullsCowsCalculator
{
    /** Calcula la cantidad de toros y vacas
     *
     * @return array Cantidad de toros y vacas
     * Estructura:
     * [
     *      'bulls': Entero. Cantidad de toros
     *      'cows' : Entero. Cantidad de vacas
     * ]
     *
     * Ej:
     * [
     *      'bulls' => 1,
     *      'cows'  => 2
     * ]
     */
    public function calculate($combination1, $combination2): array
    {
        $bulls = 0;
        $cows = 0;

        for($i = 0; $i < 4; $i++) {
            for($j = 0; $j < 4; $j++) {
                if($combination1[$i] === $combination2[$j]){
                    if($i === $j){
                        $bulls++;
                    } else {
                        $cows++;
                    }
                }
            }
        }

        return [
            'bulls' => $bulls,
            'cows' => $cows
        ];
    }
}
