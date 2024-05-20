<?php

namespace App\Classes;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiResponseClass
 *
 * Se encarga de
 *
 * @package App\Classes
 */
class ApiResponseClass
{
    /**Da formato y estructura estandarizada a los datos que se envian en la respuesta
     *
     * @param $params array Datos necesarios para crear la respuesta
     * Estructura:
     * [
     *      'success': Booleano. Indica si es una respuesta exitosa
     *      'code':    Número entero. Código HTTP para la respuesta.
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     *      'result':  Arreglo. Datos a devolver.
     * ]
     * Ej:
     * [
     *      'success' => true,
     *      'code' => 201,
     *      'message' => "El juego ha sido creado",
     *      'result' => [ 'id' => 6 ]
     * ]
     *
     * @return array Datos listos para ser enviados
     * Estructura:
     * [
     *      'success': Booleano. Indica si es una respuesta exitosa
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     *      'data':  Arreglo. Datos a devolver.
     * ]
     * Ej:
     * [
     *      'success' => false,
     *      'message' => "Los datos recibidos no son válidos",
     *      'data' => [
     *          'username' => ["El nombre de usuario es obligatorio"]
     *      ]
     * ]
     */
    private static function getResponseData($params) : array
    {
        $response=[
            'success' => $params['success']??true,
            'message' => $params['message']??"Operación exitosa",
        ];
        if(isset($params['result'])){
            $response['data'] =$params['result'];
        }

        return $response;
    }

    /**Construye la respuesta a enviar para una solicitud concluida exitosamente
     * @param $params array Datos necesarios para crear la respuesta
     * Estructura:
     * [
     *      'success': Booleano. Indica si es una respuesta exitosa
     *      'code':    Número entero. Código HTTP para la respuesta.
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     *      'result':  Arreglo. Datos a devolver.
     * ]
     * Ej:
     * [
     *      'success' => true,
     *      'code' => 201,
     *      'message' => "El juego ha sido creado",
     *      'result' => [ 'id' => 6 ]
     * ]
     *
     * @return JsonResponse Respuesta Json
     */
    public static function sendResponse($params): JsonResponse
    {
        $response = ApiResponseClass::getResponseData($params);

        return response()->json($response, $params['code']??200);
    }

    /**Contruye la respuesta par una solicitud concluida exitosamente
     * @param $params array Datos necesarios para crear la respuesta
     * Estructura:
     * [
     *      'success': Booleano. Indica si es una respuesta exitosa
     *      'code':    Número entero. Código HTTP para la respuesta.
     *      'message': Cadena de texto. Mensaje informativo sobre el resultado de la operación
     *      'result':  Arreglo. Datos a devolver.
     * ]
     * Ej:
     * [
     *      'success' => false,
     *      'code' => 422,
     *      'message' => "Los datos recibidos no son válidos",
     *      'result' => [
     *          'username' => ["El nombre de usuario es obligatorio"]
     *      ]
     * ]
     *
     * @return HttpResponseException
     */
    public static function sendExceptionResponse($params): HttpResponseException
    {
        $response = ApiResponseClass::getResponseData($params);

        throw new HttpResponseException(response()->json($response, $params['code']??200));
    }


}
