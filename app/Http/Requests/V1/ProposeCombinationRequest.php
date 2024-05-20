<?php
namespace App\Http\Requests\V1;

use App\Classes\ApiResponseClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class ProposeCombinationRequest
 *
 * Petición para proponer una combinación de dígitos, un intento: Permite validar los datos enviados antes de pasarlos al controlador
 * Si los datos recibidos no son correctos inmediatamente se devuelve una respuesta con código 422 indicando que
 * los datos enviados no pueden ser procesados y la lista de errores encontrados
 *
 * @package App\Http\Requests\V1
 */
class ProposeCombinationRequest extends FormRequest
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
            'combination' => 'required|digits:4|integer',
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
            'combination.required' => 'La combinación es un dato obligatorio obligatorio',
            'combination.digits' => 'La combinación debe tener exactamente 4 dígitos',
            'combination.integer' => 'La combinación debe ser un número entero',
            'game.required' => 'El identificador del juego es obligatorio',
            'game.integer' => 'El identificador del juego debe ser un número entero',
        ];
    }
}
