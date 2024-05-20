<?php

namespace App\Services\V1;

use App\Interfaces\V1\GameRepositoryInterface;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class DeleteGameHandler
 *
 * Servicio encargado de procesar la solicitud de eliminación de los datos de un juego y devolver los datos necesarios para
 * generar la respuesta a la solicitud.
 *
 * @package App\Services\V1
 */
class DeleteGameHandler
{
    /**
     * @var GameRepositoryInterface Repositorio de juegos
     */
    private GameRepositoryInterface $gameRepository;

    /**
     * ProposeCombinationHandler constructor.
     * @param GameRepositoryInterface $gameRepository Servicio de acceso a datos para la entidad de juegos
     */
    public function __construct(GameRepositoryInterface $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /** Elimina los datos de un juego y devuelve un arreglo con los datos
     *  necesarios para generar la respuesta.
     *
     * @param $data array Datos recibidos y previamente validados
     * Ej:
     * [
     *      'id' => '1',
     * ]
     *
     * @return array datos necesarios para generar la respuesta a la solicitud
     * Estructura:
     * [
     *      'code':    Número entero. Código HTTP para la respuesta. Valores posibles:
     *                 - 200: Combinación aceptada
     *                 - 404: El juego no existe
     *                 - 500: Ocurrió un error
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     * ]
     * Ej:
     * [
     *      'code' => 200,
     *      'message' => "El juego ha sido eliminado"
     * ]
     */
    public function handleRequestData($data): array
    {
        $game = $this->gameRepository->getById($data['id']);

        if(!$game){
            return [
                'code' => 404,
                'success' => false,
                'message' => "No existe un juego con el identificador especificado"
            ];
        }

        $cacheId = 'game_'.$game->id;

        DB::beginTransaction();
        try{

            $game = $this->gameRepository->delete($game->id);

            DB::commit();

            if(Cache::get($cacheId)){
                Cache::delete($cacheId);
            }

            return [
                'code' => 200,
                'success' => true,
                'message' => "El juego ha sido eliminado",
                'result' => $data
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
