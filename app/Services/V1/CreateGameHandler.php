<?php

namespace App\Services\V1;

use App\Interfaces\V1\GameRepositoryInterface;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class CreateGameHandler
 *
 * Servicio encargado de procesar la solicitud de creación de un juego y devolver los datos necesarios para
 * generar la respuesta a la solicitud.
 *
 * @package App\Services\V1
 */
class CreateGameHandler
{
    /**
     * @var GameRepositoryInterface Repositorio de juegos
     */
    private GameRepositoryInterface $gameRepository;

    /**
     * @var CombinationGenerator Servicio para generar cada combinación de dígitos
     */
    private CombinationGenerator $generator;

    public function __construct(GameRepositoryInterface $gameRepository, CombinationGenerator $generator)
    {
        $this->gameRepository = $gameRepository;
        $this->generator = $generator;
    }

    /** Procesa los datos de la solicitud de creación de un juego y devuelve un arreglo con los datos
     *  necesarios para generar la respuesta.
     *
     * @param $data array Datos recibidos y previamente validados
     * Ej:
     * [
     *      'username' => 'pepe21',
     *      'age' => 23
     * ]
     *
     * @return array datos necesarios para generar la respuesta a la solicitud
     * Estructura:
     * [
     *      'code':    Número entero. Código HTTP para la respuesta. Valores posibles:
     *                 - 201: El juego ha sido creado
     *                 - 500: Ocurrió un error
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     *      'result':  Arreglo. Datos a devolver sobre el juego creado. Estructura:
     *                 [
     *                      'id': Número entero. Identificador del juego creado
     *                 ]
     * ]
     * Ej:
     * [
     *      'code' => 201,
     *      'message' => "El juego ha sido creado",
     *      'result' => [ 'id' => 6 ]
     * ]
     */
    public function handleRequestData($data): array
    {
        $data['combination'] = $this->generator->generate();

        DB::beginTransaction();
        try{
            /**
             * @var $game Game
             */
            $game = $this->gameRepository->store($data);

            DB::commit();

            return [
                'code' => 201,
                'message' => "El juego ha sido creado",
                'result' => [
                    'id' => $game->id
                ]
            ];
        }catch(\Exception $ex){
            DB::rollBack();
            Log::info($ex);

            return [
                'code' => 500,
                'success' => false,
                'message' => "Ha ocurrido un error! La solicitud no pudo ser completada"
            ];
        }
    }
}
