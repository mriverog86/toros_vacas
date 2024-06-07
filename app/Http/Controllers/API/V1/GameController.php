<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateGameRequest;
use App\Http\Requests\V1\DeleteGameRequest;
use App\Http\Requests\V1\PreviousCombinationRequest;
use App\Http\Requests\V1\ProposeCombinationRequest;
use App\Services\V1\CreateGameHandler;
use App\Services\V1\DeleteGameHandler;
use App\Services\V1\PreviousCombinationHandler;
use App\Services\V1\ProposeCombinationHandler;
use OpenApi\Annotations as OA;

/**
 * Class GameController
 *
 * Controlador principal del juego. Maneja las peticiones del juego.
 *
 * @package App\Http\Controllers\API\V1
 * @OA\Info(
 *     version="1.0",
 *     title="Toros y Vacas API",
 *     description="Primer Campeonato Mundial de Toros y Vacas"
 * )
 */
class GameController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/game/create",
     *     summary="Crea un nuevo juego",
     *     tags={"Juego"},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="username", type="string"),
     *                      @OA\Property(property="age", type="integer")
     *                  ),
     *                  example={ "username":"pepe23", "age":"23"}
     *              )
     *          )
     *     ),
     *     @OA\Response(response=201, description="Juego creado",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="true"),
     *                      @OA\Property(property="message", type="string", example="El juego ha sido creado"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example="5"),
     *                      ),
     *                      example={ "success":true, "message":"El juego ha sido creado", "data":{ "id": 5 } }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=422, description="Error al validar los datos recibidos",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="Los datos recibidos no son válidos"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property( property="username", type="array", @OA\Items(type="string", example="La longitud máxima para el nombre de usuario es de 50 carácteres")),
     *                          @OA\Property( property="age", type="array", @OA\Items(type="string", example="La edad debe ser un número entero"))
     *                      ),
     *                      example={
     *                          "success":false,
     *                          "message":"Los datos recibidos no son válidos",
     *                          "data":{
     *                              "username": {"El nombre de usuario solo puede contener letras y números"},
     *                              "age": {"La edad debe ser un número entero"}
     *                          }
     *                      }
     *                  )
     *              )
     *          }
     *      )
     * )
     *
     * Crea un juego
     *
     * @param CreateGameRequest $request Petición personalizada con los datos necesarios para crear el juego
     * @param CreateGameHandler $handler Servicio encargado de manejar la creación del juego y devolver la información a enviar en la respuesta
     * @return \Illuminate\Http\JsonResponse Se devuelve una respuesta en formato Json con datos del juego creado
     * Estructura:
     * [
     *      'code':    Número entero. Código HTTP para la respuesta. Valores posibles:
     *                 - 201: El juego ha sido creado
     *                 - 422: Error al validar los datos recibidos
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
    public function create(CreateGameRequest $request, CreateGameHandler $handler)
    {
        // Obteniendo los datos enviados ya validados...
        $validated = $request->validated();

        return ApiResponseClass::sendResponse($handler->handleRequestData($validated));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/game/propose_combination",
     *     summary="Valida los dígitos propuestos para un intento",
     *     tags={"Juego"},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="object",
     *                      @OA\Property(property="combination", description="Número de cuatro dígitos", type="integer", example="8541"),
     *                      @OA\Property(property="game", description="Identificador del juego", type="integer", example="6")
     *                  ),
     *                  example={ "combination":"8541", "game":"6"}
     *              )
     *          )
     *     ),
     *     @OA\Response(response=200, description="Combinación aceptada",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="true"),
     *                      @OA\Property(property="message", type="string", example="La combinación ha sido aceptada"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(property="combination", description="La combinación propuesta", type="integer", example="5623"),
     *                          @OA\Property(property="bulls", description="Cantidad de toros obtenidos", type="integer", example="1"),
     *                          @OA\Property(property="cows", description="Cantidad de vacas obtenidas", type="integer", example="2"),
     *                          @OA\Property(property="attempts", description="Cantidad de intentos", type="integer", example="6"),
     *                          @OA\Property(property="time_available", description="Tiempo disponible en segundos", type="integer", example="100"),
     *                          @OA\Property(property="score", description="Evaluación", type="integer", example="200"),
     *                          @OA\Property(property="ranking", description="Posición respecto del juego actual respecto a otros juegos", type="integer", example="15"),
     *                      ),
     *                      example={ "success":true, "message":"La combinación ha sido aceptada", "data":{ "combination": 5623, "bulls": 1, "cows": 2, "attempts": 6, "time_available": 100, "score": 200, "ranking":15 } }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=208, description="Combinación duplicada",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="Combinación duplicada"),
     *                      example={
     *                          "success":false,
     *                          "message":"Combinación duplicada",
     *                      }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=404, description="El juego no existe",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="El juego no existe"),
     *                      example={
     *                          "success":false,
     *                          "message":"No existe un juego con el identificador especificado",
     *                      }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=410, description="Juego terminado",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="Se ha agotado el tiempo para el juego"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(property="combination", description="La combinación que se debía adivinar", type="integer", example="5623")
     *                      ),
     *                      example={
     *                          "success":false,
     *                          "message":"Se ha agotado el tiempo para el juego",
     *                          "data":{ "combination": 5623 }
     *                      }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=422, description="Error al validar los datos recibidos",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="Los datos recibidos no son válidos"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property( property="combination", type="array", @OA\Items(type="integer", example="La combinación debe tener exactamente 4 dígitos")),
     *                          @OA\Property( property="game", type="array", @OA\Items(type="integer", example="El identificador del juego debe ser un número entero"))
     *                      ),
     *                      example={
     *                          "success":false,
     *                          "message":"Los datos recibidos no son válidos",
     *                          "data":{
     *                              "combinación": {"La combinación debe tener exactamente 4 dígitos"},
     *                              "game": {"El identificador del juego debe ser un número entero"}
     *                          }
     *                      }
     *                  )
     *              )
     *          }
     *     )
     * )
     */
    public function proposeCombination(ProposeCombinationRequest $request, ProposeCombinationHandler $handler)
    {
        // Obteniendo los datos enviados ya validados...
        $validated = $request->validated();

        return ApiResponseClass::sendResponse($handler->handleRequestData($validated));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/game/previous_combination",
     *     summary="Dado un número de intento devuelve la respuesta correspondiente al intento anterior",
     *     tags={"Juego"},
     *     @OA\Parameter(name="attempt", description="Número del intento", in="query", example="4"),
     *     @OA\Parameter(name="game", description="Identificador del juego", in="query", example="6"),
     *     @OA\Response(response=200, description="Combinación aceptada",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="true"),
     *                      @OA\Property(property="message", type="string", example="La combinación ha sido aceptada"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(property="combination", description="La combinación propuesta", type="integer", example="5623"),
     *                          @OA\Property(property="bulls", description="Cantidad de toros obtenidos", type="integer", example="1"),
     *                          @OA\Property(property="cows", description="Cantidad de vacas obtenidas", type="integer", example="2"),
     *                          @OA\Property(property="attempts", description="Cantidad de intentos", type="integer", example="6"),
     *                          @OA\Property(property="time_available", description="Tiempo disponible en segundos", type="integer", example="100"),
     *                          @OA\Property(property="score", description="Evaluación", type="integer", example="200"),
     *                          @OA\Property(property="ranking", description="Posición respecto del juego actual respecto a otros juegos", type="integer", example="15"),
     *                      ),
     *                      example={ "success":true, "message":"La combinación ha sido aceptada", "data":{ "combination": 5623, "bulls": 1, "cows": 2, "attempts": 6, "time_available": 100, "score": 200, "ranking":15 } }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=404, description="El juego o el intento no existe",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="El juego no existe"),
     *                      example={
     *                          "success":false,
     *                          "message":"No existe un juego con el identificador especificado o no existe un intento con tal número",
     *                      }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=422, description="Error al validar los datos recibidos",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="Los datos recibidos no son válidos"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property( property="attempt", type="array", @OA\Items(type="integer", example="El número de intento debe ser mayor que 1")),
     *                          @OA\Property( property="game", type="array", @OA\Items(type="integer", example="El identificador del juego debe ser un número entero"))
     *                      ),
     *                      example={
     *                          "success":false,
     *                          "message":"Los datos recibidos no son válidos",
     *                          "data":{
     *                              "attempt": {"El número de intento debe ser mayor que 1"},
     *                              "game": {"El identificador del juego debe ser un número entero"}
     *                          }
     *                      }
     *                  )
     *              )
     *          }
     *     )
     * )
     */
    public function previousCombination(PreviousCombinationRequest $request, PreviousCombinationHandler $handler)
    {
        // Obteniendo los datos enviados ya validados...
        $validated = $request->validated();

        return ApiResponseClass::sendResponse($handler->handleRequestData($validated));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/game/delete",
     *     summary="Elimina los datos de un juego",
     *     tags={"Juego"},
     *     @OA\Parameter(name="id", description="Identificador del juego", in="query", example="6"),
     *     @OA\Response(response=200, description="Juego eliminado",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="true"),
     *                      @OA\Property(property="message", type="string", example="El juego ha sido eliminado"),
     *                      example={ "success":true, "message":"El juego ha sido eliminado" }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=404, description="El juego no existe",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="No existe un juego con el identificador especificado"),
     *                      example={
     *                          "success":false,
     *                          "message":"No existe un juego con el identificador especificado",
     *                      }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(response=422, description="Error al validar los datos recibidos",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(property="success", type="boolean", example="false"),
     *                      @OA\Property(property="message", type="string", example="Los datos recibidos no son válidos"),
     *                      @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property( property="id", type="array", @OA\Items(type="integer", example="El identificador del juego debe ser un número entero"))
     *                      ),
     *                      example={
     *                          "success":false,
     *                          "message":"Los datos recibidos no son válidos",
     *                          "data":{
     *                              "id": {"El identificador del juego debe ser un número entero"}
     *                          }
     *                      }
     *                  )
     *              )
     *          }
     *     )
     * )
     */
    public function delete(DeleteGameRequest $request, DeleteGameHandler $handler)
    {
        // Obteniendo los datos enviados ya validados...
        $validated = $request->validated();

        return ApiResponseClass::sendResponse($handler->handleRequestData($validated));
    }
}
