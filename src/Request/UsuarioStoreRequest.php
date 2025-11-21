<?php

namespace App\Request;

class UsuarioStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nome' => 'required|min:3',
            'usuario' => 'required|unique:usuarios,usuario',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|min:6',
            'nivel' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo Nome é obrigatório.',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'usuario.required' => 'O campo Usuário é obrigatório.',
            'usuario.unique' => 'Este usuário já está em uso.',
            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'Email inválido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'senha.required' => 'O campo Senha é obrigatório.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'nivel.required' => 'O nível é obrigatório.'
        ];
    }
}