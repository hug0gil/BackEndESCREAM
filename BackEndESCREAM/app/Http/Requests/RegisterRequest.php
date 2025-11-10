<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RegisterRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Primero definimos las reglas base
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'plan_id' => 'required|integer|in:1,2,3',
        ];

        // Solo añadir admin_level si la ruta es admin.register
        if ($this->is('api/admin/register')) {
            $rules['admin_level'] = 'required|integer|in:1,2,3';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'email.required' => 'The email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'email.unique' => 'This email is already in use.',

            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 6 characters long.',

            'plan_id.required' => 'The plan id is required.',
            'plan_id.integer' => 'The plan id must be an integer.',
            'plan_id.in' => 'The selected plan id is invalid. It must be 1, 2 or 3.',

            'admin_level.required' => 'The admin level is required.',
            'admin_level.integer' => 'The admin level must be an integer.',
            'admin_level.in' => 'The admin level must be 1, 2, or 3.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Log de validación fallida
        Log::error('Validation failed for user registration', [
            'errors' => $validator->errors()->toArray(), // Metemos en el log TODOS los errores
            'input' => $this->except(['password', 'password_confirmation', 'token']),
            'ip' => $this->ip(),
        ]);

        /* 
        Lanza la excepción para que Laravel devuelva la respuesta JSON 422 IMPORTANTE,
        si no seguirá su flujo como si fuera válido

        Se utiliza cuando la solicitud es sintácticamente correcta (por eso no sería un 400 Bad Request), 
        pero el servidor no puede procesarla debido a errores de validación de datos o lógica de negocio.
        */
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
