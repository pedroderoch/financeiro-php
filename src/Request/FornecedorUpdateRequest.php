<?php

namespace App\Request;

class FornecedorUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nome' => 'required|string|min:3|max:255',
            'descricao' => 'nullable|string|min:3|max:255',
            'chave_pix' => 'nullable|string|min:3|max:255',
            'foto' => 'nullable'
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo Nome é obrigatório.',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.max' => 'O nome deve ter no máximo 255 caracteres.',
            'descricao.min' => 'A descricao deve ter pelo menos 3 caracteres.',
            'descricao.max' => 'A descricao deve ter no máximo 255 caracteres.',
            'chave_pix.min' => 'A Chave Pix deve ter pelo menos 3 caracteres.',
            'chave_pix.max' => 'A Chave Pix deve ter no máximo 255 caracteres.'
        ];
    }
}