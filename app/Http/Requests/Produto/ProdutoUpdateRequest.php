<?php

namespace App\Http\Requests\Produto;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Produto;
use App\Helpers\VerificaEmpresa;

class ProdutoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $produtoId = $this->route('id');

        // Se não tem ID na rota, não pode autorizar
        if (!$produtoId) {
            return false;
        }

        // Buscar o produto para verificar a empresa
        $produto = Produto::find($produtoId);

        if (!$produto) {
            return false;
        }

        // Verificar se o usuário tem acesso à empresa do produto
        return VerificaEmpresa::verificaEmpresaPertenceAoUsuario($produto->empresa_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $produtoId = $this->route('id');

        return [
            // Relacionamentos opcionais
            'categoria_id' => 'sometimes|nullable|exists:categorias,id',
            'unidade_medida_id' => 'sometimes|nullable|exists:unidades_medidas,id',

            // Dados do produto (todos opcionais para update)
            'tipo' => 'sometimes|nullable|in:produto,servico',
            'nome' => 'sometimes|nullable|string|max:255',
            'imagem' => 'sometimes|nullable|string|max:500',
            'slug' => 'sometimes|nullable|string|max:255|unique:produtos,slug,' . $produtoId,
            'descricao' => 'sometimes|nullable|string|max:1000',
            'preco' => 'sometimes|nullable|numeric|min:0|max:999999.99',
            'estoque' => 'sometimes|nullable|numeric|min:0|max:999999.999',
            'destaque' => 'sometimes|nullable|boolean',
            'ativo' => 'sometimes|nullable|boolean',
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
            // Relacionamentos
            'categoria_id.exists' => 'A categoria selecionada não existe.',
            'unidade_medida_id.exists' => 'A unidade de medida selecionada não existe.',

            // Dados do produto
            'tipo.in' => 'O tipo deve ser "produto" ou "servico".',

            'nome.string' => 'O nome deve ser um texto válido.',
            'nome.max' => 'O nome não pode ter mais que 255 caracteres.',

            'imagem.string' => 'A imagem deve ser um texto válido.',
            'imagem.max' => 'O caminho da imagem não pode ter mais que 500 caracteres.',

            'slug.string' => 'O slug deve ser um texto válido.',
            'slug.max' => 'O slug não pode ter mais que 255 caracteres.',
            'slug.unique' => 'Este slug já está sendo usado por outro produto.',

            'descricao.string' => 'A descrição deve ser um texto válido.',
            'descricao.max' => 'A descrição não pode ter mais que 1000 caracteres.',

            'preco.numeric' => 'O preço deve ser um valor numérico.',
            'preco.min' => 'O preço não pode ser negativo.',
            'preco.max' => 'O preço não pode ser maior que 999.999,99.',

            'estoque.numeric' => 'O estoque deve ser um valor numérico.',
            'estoque.min' => 'O estoque não pode ser negativo.',
            'estoque.max' => 'O estoque não pode ser maior que 999.999,999.',

            'destaque.boolean' => 'O campo destaque deve ser verdadeiro ou falso.',
            'ativo.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
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
            // Verificar se já existe um produto com o mesmo nome nesta empresa (exceto o atual)
            $produtoId = $this->route('id');
            $nome = $this->input('nome');

            if ($nome && $produtoId) {
                $produto = Produto::find($produtoId);

                if ($produto) {
                    $existe = Produto::where('empresa_id', $produto->empresa_id)
                        ->where('nome', $nome)
                        ->where('id', '!=', $produtoId)
                        ->exists();

                    if ($existe) {
                        $validator->errors()->add('nome', 'Já existe um produto com este nome nesta empresa.');
                    }
                }
            }

            // Se forneceu nome mas não forneceu slug, gerar automaticamente
            if ($this->has('nome') && (!$this->has('slug') || empty($this->slug))) {
                $this->merge(['slug' => \Illuminate\Support\Str::slug($this->nome)]);
            }
        });
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
                'message' => 'Você não tem permissão para editar este produto.'
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
            'message' => 'Dados inválidos. Verifique os erros abaixo.',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}