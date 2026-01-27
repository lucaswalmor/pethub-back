<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\VerificaEmpresa;

class PedidoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $pedidoId = $this->route('id');

        if (!$pedidoId) {
            return false;
        }

        // Apenas empresas podem alterar pedidos
        $pedido = \App\Models\Pedido::find($pedidoId);

        if (!$pedido) {
            return false;
        }

        return VerificaEmpresa::verificaEmpresaPertenceAoUsuario($pedido->empresa_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_pedido_id' => 'sometimes|exists:status_pedidos,id',
            'observacoes' => 'sometimes|nullable|string|max:1000',
            'status_observacoes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status_pedido_id.exists' => 'O status selecionado não existe.',

            'observacoes.string' => 'As observações devem ser um texto válido.',
            'observacoes.max' => 'As observações não podem ter mais que 1000 caracteres.',

            'status_observacoes.string' => 'As observações do status devem ser um texto válido.',
            'status_observacoes.max' => 'As observações do status não podem ter mais que 500 caracteres.',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'error' => 'Acesso negado',
                'message' => 'Você não tem permissão para alterar este pedido.'
            ], 403)
        );
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => 'Dados inválidos para atualização do pedido.',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}