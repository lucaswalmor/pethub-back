<?php

namespace App\Http\Requests\EmpresaAvaliacao;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\EmpresaAvaliacao;

class EmpresaAvaliacaoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificações de autorização são feitas no controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pedido_id' => 'required|exists:pedidos,id',
            'empresa_id' => 'nullable|exists:empresas,id', // Opcional para validação extra
            'nota' => 'required|numeric|min:1|max:5|regex:/^\d+(\.0|\.5)?$/', // Apenas .0 ou .5
            'descricao' => 'nullable|string|max:1000',
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
            'pedido_id.required' => 'O pedido é obrigatório.',
            'pedido_id.exists' => 'O pedido selecionado não existe.',

            'empresa_id.exists' => 'A empresa selecionada não existe.',

            'nota.required' => 'A nota é obrigatória.',
            'nota.numeric' => 'A nota deve ser um valor numérico.',
            'nota.min' => 'A nota deve ser no mínimo 1.',
            'nota.max' => 'A nota deve ser no máximo 5.',
            'nota.regex' => 'A nota deve ter apenas uma casa decimal (.0 ou .5).',

            'descricao.string' => 'A descrição deve ser um texto válido.',
            'descricao.max' => 'A descrição não pode ter mais que 1000 caracteres.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $pedidoId = $this->input('pedido_id');

            if ($pedidoId) {
                // Verificar se usuário pode avaliar este pedido
                $validacao = EmpresaAvaliacao::usuarioPodeAvaliarPedido(
                    Auth::id(),
                    $pedidoId
                );

                if (!$validacao['pode']) {
                    $validator->errors()->add('pedido_id', $validacao['motivo']);
                }
            }
        });
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
            'message' => 'Dados inválidos para avaliação.',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}