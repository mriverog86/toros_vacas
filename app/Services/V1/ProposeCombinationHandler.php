<?php

namespace App\Services\V1;

use App\Interfaces\V1\GameRepositoryInterface;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProposeCombinationHandler
 *
 * Servicio encargado de procesar la solicitud de propuesta de una combinación y devolver los datos necesarios para
 * generar la respuesta a la solicitud.
 *
 * @package App\Services\V1
 */
class ProposeCombinationHandler
{
    /**
     * @var GameRepositoryInterface Repositorio de juegos
     */
    private GameRepositoryInterface $gameRepository;

    /**
     * @var BullsCowsCalculator Servicio que calcula la cantidad de toros y vacas entre dos combinaciones de dígistos
     */
    private BullsCowsCalculator $bullsCowsCalculator;

    /**
     * @var integer $gameTime Tiempo de diración máximo para un juego
     */
    private $gameTime;

    /**
     * ProposeCombinationHandler constructor.
     * @param GameRepositoryInterface $gameRepository Servicio de acceso a datos para la entidad de juegos
     * @param BullsCowsCalculator $bullsCowsCalculator Servicio para calcular la cantidad de toros y vacas entredos cadenas de dígitos
     * @param $gameTime integer Tiempo máximo para un juego, se carga desde la configuración de la aplicación app.game_time y se inyecta como una dependencia
     */
    public function __construct(GameRepositoryInterface $gameRepository, BullsCowsCalculator $bullsCowsCalculator, $gameTime)
    {
        $this->gameRepository = $gameRepository;
        $this->bullsCowsCalculator = $bullsCowsCalculator;
        $this->gameTime = $gameTime;
    }

    /** Procesa los datos de la solicitud de propuesta de una combinación y devuelve un arreglo con los datos
     *  necesarios para generar la respuesta.
     *
     * @param $data array Datos recibidos y previamente validados
     * Ej:
     * [
     *      'combination' => '8562',
     *      'game' => '23'
     * ]
     *
     * @return array datos necesarios para generar la respuesta a la solicitud
     * Estructura:
     * [
     *      'code':    Número entero. Código HTTP para la respuesta. Valores posibles:
     *                 - 200: Combinación aceptada
     *                 - 208: Combinación duplicada
     *                 - 404: El juego no existe
     *                 - 410: Juego terminado
     *                 - 500: Ocurrió un error
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     *      'result':  Arreglo. Datos a devolver sobre el resultado del intento.
     *                 Estructura si se acepta la combinación:
     *                 [
     *                      'combination':    Número de 4 dígitos. La combinación enviada
     *                      'bulls':          Entero. Cantidad de toros conseguidos
     *                      'cows':           Entero. Cantidad de vacas conseguidos
     *                      'attempts':       Entero. Cantidad de intentos
     *                      'time_available': Entero. Tiempo de juego restante en segundos
     *                      'score':          Entero. Puntuación obtenida
     *                      'ranking':        Entero. Posición respecto a otros juegos
     *                 ]
     *                 Estructura si el tiempo se ha terminado y el juego finalizó:
     *                 [
     *                      'combination':    Número de 4 dígitos. La combinación que se debía adivinar
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
        $game = $this->gameRepository->getById($data['game']);
        $cacheId = 'game_'.$game->id;

        if(!$game){
            return [
                'code' => 404,
                'success' => false,
                'message' => "No existe un juego con el identificador especificado"
            ];
        }

        $cached = Cache::get($cacheId)??[];
        if(!empty($cached)){
            for ($i = 0; $i < sizeof($cached); $i++){
                if($data['combination'] === $cached[$i]['combination']){
                    return [
                        'code' => 208,
                        'success' => false,
                        'message' => "Combinación duplicada"
                    ];
                }
            }
        }

        $time_available = $this->getGameAvailableTime($game);

        if($time_available === 0){
            return [
                'code' => 410,
                'success' => false,
                'message' => "Se ha agotado el tiempo para el juego",
                'result' => [ 'combination' => $game->combination ]
            ];
        }

        //Los intentos se determinan por los intentos guardados en cache + 1, el actual
        $attempts = sizeof($cached) + 1;

        //Los datos a actualizar en el juego
        $updatedData = [];

        $bullsCows = $this->bullsCowsCalculator->calculate($game->combination, $data['combination']);
        if($bullsCows['bulls'] === 4){
            $updatedData['won'] = true;
        }

        $score = (int)($this->getGameElapsedTime($game)/2 + $attempts);
        $updatedData['score'] = $score;


        DB::beginTransaction();
        try{
            /**
             * @var $game Game
             */
            $game = $this->gameRepository->update($updatedData, $game->id);

            DB::commit();

            $data = [
                'combination' => $data['combination'],
                'bulls' => $bullsCows['bulls'],
                'cows' => $bullsCows['cows'],
                'attempts' => $attempts,
                'time_available' => $time_available,
                'score' => $score
            ];

            //Guardando los datos en la cache por el tiempo disponible para el juego mas un minuto
            $cached[] = $data;
            Cache::put($cacheId, $cached, $time_available + 60);

            return [
                'code' => 200,
                'success' => true,
                'message' => "La combinación ha sido aceptada",
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

    /** Devuelve el tiempo transcurrido para un juego
     *
     * @param $game
     * @return int El tiempo en segundos
     */
    protected function getGameElapsedTime($game): int
    {
        $created = Carbon::parse($game->created_at);
        $now = Carbon::now();

        return (int)$created->diffInSeconds($now);
    }

    /** Devuelve el tiempo aún disponible para el juego
     *
     * @param $game
     * @return int El tiempo en segundos
     */
    protected function getGameAvailableTime($game): int
    {
        $elapsed = $this->getGameElapsedTime($game);

        return $elapsed < $this->gameTime ? $this->gameTime - $elapsed : 0;

    }
}
