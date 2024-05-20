<?php
namespace App\Http\Requests\V1;

use App\Classes\ApiResponseClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class PreviousCombinationRequest
 *
 * Petición para obtener el resultado para el intento anterior: Permite validar los datos enviados antes de pasarlos al controlador
 * Si los datos recibidos no son correctos inmediatamente se devuelve una respuesta con código 422 indicando que
 * los datos enviados no pueden ser procesados y la lista de errores encontrados
 *
 * @package App\Http\Requests\V1
 */
class PreviousCombinationRequest extends FormRequest
{
    /**
     * Determina si el usuario esta autorizado a realizar la petición
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Lista de reglas de validación para cada uno de los parámetros de la petición
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attempt' => 'integer|min:2',
            'game' => 'required|integer'
        ];
    }

    /** Se ejecuta cuando falla la validación de los datos enviados en la petición
     *
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        //Devolviendo una respuesta de error 422: Los datos enviados no pueden ser procesados
        ApiResponseClass::sendExceptionResponse([
            'success' => false,
            'message' => 'Los datos recibidos no son válidos',
            'result'    => $validator->errors(),
            'code'    => 422
        ]);
    }

    /**
     * Devuelve los mensajes de error para cada regla de validación a aplicar a los datos contenidos en la petición
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'attempt.integer' => 'El número del intento debe ser un número entero',
            'attempt.min' => 'El número del intento debe mayor o igual que 2',
            'game.required' => 'El identificador del juego es obligatorio',
            'game.integer' => 'El identificador del juego debe ser un número entero',
        ];
    }
}
