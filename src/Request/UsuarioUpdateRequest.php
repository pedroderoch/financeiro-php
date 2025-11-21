<?php

namespace App\Request;

class UsuarioUpdateRequest extends BaseRequest
{
    private int $id;

    // 1. CONSTRUTOR PARA RECEBER O ID
    // Precisamos saber QUEM estamos editando para ignorar o ID dele na validação
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function rules(): array
    {
        return [
            'nome'    => 'required|min:3',
            // 2. REGRA UNIQUE COM EXCEÇÃO
            // Concatenamos o ID no final para o Validador ignorar este usuário
            'usuario' => 'required|unique:usuarios,usuario,' . $this->id,
            'email'   => 'required|email|unique:usuarios,email,' . $this->id,
            // 3. SENHA OPCIONAL
            // Se vier a senha no formulário, tem que ter min 6. Se não vier (vazio), passa (nullable).
            'senha'   => 'nullable|min:6',
            'nivel'   => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'    => 'O campo Nome é obrigatório.',
            'usuario.required' => 'O campo Usuário é obrigatório.',
            'usuario.unique'   => 'Este usuário já está em uso.',
            'email.required'   => 'O campo E-mail é obrigatório.',
            'email.unique'     => 'Este e-mail já está em uso.',
            'senha.min'        => 'A senha deve ter pelo menos 6 caracteres.',
            'nivel.required'   => 'O nível é obrigatório.'
        ];
    }
}