<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpresaStoreRequest extends FormRequest
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
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
                Rule::unique('empresas', 'cnpj')->whereNull('deleted_at'),
            ],
            'email' => ['required', 'email', 'max:255'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'ativo' => ['boolean'],
            'data_adesao' => ['nullable', 'date'],
            'data_expiracao' => ['nullable', 'date', 'after:data_adesao'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'razao_social.required' => 'A razão social é obrigatória.',
            'razao_social.max' => 'A razão social não pode ter mais de 255 caracteres.',
            'nome_fantasia.required' => 'O nome fantasia é obrigatório.',
            'nome_fantasia.max' => 'O nome fantasia não pode ter mais de 255 caracteres.',
            'cnpj.regex' => 'O CNPJ deve estar no formato XX.XXX.XXX/XXXX-XX.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço válido.',
            'data_expiracao.after' => 'A data de expiração deve ser posterior à data de adesão.',
            'logo.file' => 'O logo deve ser um arquivo.',
            'logo.mimes' => 'A logo deve ser um arquivo do tipo: jpeg, png, jpg, gif ou webp.',
            'logo.max' => 'A logo não pode ser maior que 2MB.',
        ];
    }
}
