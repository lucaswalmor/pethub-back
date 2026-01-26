<?php

namespace App\Http\Requests\Empresa;

use Illuminate\Foundation\Http\FormRequest;

class EmpresaUploadImageRequest extends FormRequest
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
        $tipo = $this->query('tipo');

        if ($tipo === 'banner') {
            return [
                'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ];
        } elseif ($tipo === 'logo') {
            return [
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ];
        }

        return [
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'banner.required' => 'O banner é obrigatório.',
            'banner.image' => 'O banner deve ser uma imagem válida.',
            'banner.mimes' => 'O banner deve ser um arquivo do tipo: jpeg, png, jpg, gif.',
            'banner.max' => 'O banner não pode ter mais que 5MB.',

            'logo.required' => 'A logo é obrigatória.',
            'logo.image' => 'A logo deve ser uma imagem válida.',
            'logo.mimes' => 'A logo deve ser um arquivo do tipo: jpeg, png, jpg, gif.',
            'logo.max' => 'A logo não pode ter mais que 2MB.',
        ];
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
            'message' => 'Dados inválidos para upload de imagem.',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}