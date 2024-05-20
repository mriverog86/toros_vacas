<?php
namespace App\Http\Requests\V1;

use App\Classes\ApiResponseClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class CreateGameRequest
 *
 * Petición de creación de un juego: Permite validar los datos enviados antes de pasarlos al controlador
 * Si los datos recibidos no son correctos inmediatamente se devuelve una respuesta con código 422 indicando que
 * los datos enviados no pueden ser procesados y la lista de errores encontrados
 *
 * @package App\Http\Requests\V1
 */
class CreateGameRequest extends FormRequest
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
            'username' => 'required|max:50|alpha_num:ascii',
            'age' => 'required|integer'
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
            'username.required' => 'El nombre de usuario es obligatorio',
            'username.alpha_num' => 'El nombre de usuario solo puede contener letras y números',
            'username.max' => 'La longitud máxima para el nombre de usuario es de 50 carácteres',
            'age.required' => 'La edad es obligatoria',
            'age.integer' => 'La edad debe ser un número entero',
        ];
    }
}
