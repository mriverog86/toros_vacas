<?php

namespace App\Services\V1;

use App\Interfaces\V1\GameRepositoryInterface;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class PreviousCombinationHandler
 *
 * Servicio encargado de procesar la solicitud de la respuesta correspondiente a la combinación anterior y devolver los datos necesarios para
 * generar la respuesta a la solicitud.
 *
 * @package App\Services\V1
 */
class PreviousCombinationHandler
{
    /** Procesa los datos de la solicitud de la respuesta a la combinación anterior y devuelve un arreglo con los datos
     *  necesarios para generar la respuesta.
     *
     * @param $data array Datos recibidos y previamente validados
     * Estructura:
     * [
     *      'attempt': Entero. Número del intento para el que se desea la respuesta anterior. Si no se envía se devuelve la ultima respuesta
     *      'game'   : Entero. Identificador del juego
     * ]
     * Ej:
     * [
     *      'attempt' => '2',
     *      'game' => '23'
     * ]
     *
     * @return array datos necesarios para generar la respuesta a la solicitud
     * Estructura:
     * [
     *      'code':    Número entero. Código HTTP para la respuesta. Valores posibles:
     *                 - 200: Combinación aceptada
     *                 - 404: El juego o el intento no existe
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     *      'result':  Arreglo. Datos a devolver sobre el resultado del intento.
     *                 [
     *                      'combination':    Número de 4 dígitos. La combinación enviada
     *                      'bulls':          Entero. Cantidad de toros conseguidos
     *                      'cows':           Entero. Cantidad de vacas conseguidos
     *                      'attempts':       Entero. Cantidad de intentos
     *                      'time_available': Entero. Tiempo de juego restante en segundos
     *                      'score':          Entero. Puntuación obtenida
     *                      'ranking':        Entero. Posición respecto a otros juegos
     *                 ]
     * ]
     * Ej:
     * [
     *      'code' => 200,
     *      'message' => "La combinación ha sido aceptada",
     *      'result' => [
     *          'combination':    7845
     *          'bulls':          1
     *          'cows':           2
     *          'attempts':       3
     *          'time_available': 200
     *          'score':          200
     *          'ranking':        15
     *      ]
     * ]
     */
    public function handleRequestData($data): array
    {
        $cacheId = 'game_'.$data['game'];

        $cached = Cache::get($cacheId);
        if(!$cached || (isset($data['attempt']) && !isset($cached[$data['attempt'] - 2]))){
            return [
                'code' => 404,
                'success' => false,
                'message' => "No existe un juego con el identificador especificado o no existe un intento con tal número"
            ];
        }

        return [
            'code' => 200,
            'success' => true,
            'message' => "La combinación ha sido aceptada",
            //Si se pasa el número del intento se devuelve la respuesta anterior, sino se devuelve la última respuesta enviada para el juego
            'result' => $cached[isset($data['attempt']) ? $data['attempt'] - 2 : sizeof($cached)-1 ]
        ];
    }
}
