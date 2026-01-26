<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UsuarioStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            // Campos principais do usuário
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'telefone' => 'required|string|max:20',
            'permissao_id' => 'sometimes|nullable|exists:permissoes,id',
            'empresa_id' => 'sometimes|nullable|exists:empresas,id',

            // Endereço (obrigatório apenas para clientes, opcional para funcionários)
            'endereco' => 'sometimes|nullable|array',
            'endereco.cep' => 'sometimes|nullable|string|max:10',
            'endereco.rua' => 'sometimes|required|string|max:255',
            'endereco.numero' => 'sometimes|required|string|max:20',
            'endereco.complemento' => 'sometimes|nullable|string|max:255',
            'endereco.bairro' => 'sometimes|nullable|string|max:255',
            'endereco.cidade' => 'sometimes|nullable|string|max:255',
            'endereco.estado' => 'sometimes|nullable|string|size:2',
            'endereco.ponto_referencia' => 'sometimes|nullable|string|max:255',
            'endereco.observacoes' => 'sometimes|nullable|string|max:500',
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
            // Campos principais do usuário
            'nome.required' => 'O nome do usuário é obrigatório.',
            'nome.string' => 'O nome deve ser um texto válido.',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres.',

            'email.required' => 'O email é obrigatório.',
            'email.string' => 'O email deve ser um texto válido.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.max' => 'O email não pode ter mais que 255 caracteres.',
            'email.unique' => 'Este email já está sendo usado por outro usuário.',

            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',

            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.string' => 'O telefone deve ser um texto válido.',
            'telefone.max' => 'O telefone não pode ter mais que 20 caracteres.',

            'permissao_id.exists' => 'A permissão selecionada não existe.',

            'empresa_id.exists' => 'A empresa selecionada não existe.',

            // Endereço
            'endereco.array' => 'Os dados de endereço devem ser um objeto válido.',

            'endereco.cep.string' => 'O CEP deve ser um texto válido.',
            'endereco.cep.max' => 'O CEP não pode ter mais que 10 caracteres.',

            'endereco.rua.required' => 'A rua é obrigatória.',
            'endereco.rua.string' => 'A rua deve ser um texto válido.',
            'endereco.rua.max' => 'A rua não pode ter mais que 255 caracteres.',

            'endereco.numero.required' => 'O número é obrigatório.',
            'endereco.numero.string' => 'O número deve ser um texto válido.',
            'endereco.numero.max' => 'O número não pode ter mais que 20 caracteres.',

            'endereco.complemento.string' => 'O complemento deve ser um texto válido.',
            'endereco.complemento.max' => 'O complemento não pode ter mais que 255 caracteres.',

            'endereco.bairro.string' => 'O bairro deve ser um texto válido.',
            'endereco.bairro.max' => 'O bairro não pode ter mais que 255 caracteres.',

            'endereco.cidade.string' => 'A cidade deve ser um texto válido.',
            'endereco.cidade.max' => 'A cidade não pode ter mais que 255 caracteres.',

            'endereco.estado.string' => 'O estado deve ser um texto válido.',
            'endereco.estado.size' => 'O estado deve ter exatamente 2 caracteres (sigla).',

            'endereco.ponto_referencia.string' => 'O ponto de referência deve ser um texto válido.',
            'endereco.ponto_referencia.max' => 'O ponto de referência não pode ter mais que 255 caracteres.',

            'endereco.observacoes.string' => 'As observações devem ser um texto válido.',
            'endereco.observacoes.max' => 'As observações não podem ter mais que 500 caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Se for FormData, precisamos converter arrays aninhados
        if ($this->isMethod('post') && !$this->hasHeader('Content-Type', 'application/json')) {
            $data = $this->all();

            // Converter arrays de formulário para arrays PHP
            if (isset($data['endereco']) && is_array($data['endereco'])) {
                $this->merge(['endereco' => $data['endereco']]);
            }
        }

        // Se for funcionário (empresa_id presente), remova o endereço enviado
        if ($this->input('empresa_id') && $this->has('endereco')) {
            $data = $this->all();
            unset($data['endereco']);
            $this->replace($data);
        }
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
            // Se for cliente (sem empresa_id), deve enviar endereço
            if (!$this->input('empresa_id') && !$this->has('endereco')) {
                $validator->errors()->add('endereco', 'Clientes devem enviar os dados de endereço.');
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
            'message' => 'Dados inválidos. Verifique os erros abaixo.',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}