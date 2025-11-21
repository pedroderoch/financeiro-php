<?php

namespace App\Request;

abstract class BaseRequest
{
    /**
     * Define as regras de validação (cada filho implementa a sua)
     */
    abstract public function rules(): array;

    /**
     * Define as mensagens personalizadas (cada filho implementa a sua)
     */
    abstract public function messages(): array;

    /**
     * O método mágico que faz tudo.
     * Recebe os dados ($_POST) e para onde voltar se der erro.
     */
    public function validate(array $data, string $redirectUrl): array
    {
        // 1. Chama a nossa função helper global 'validator'
        // Usando as regras e mensagens definidas na classe filha
        $validador = validator($data, $this->rules(), $this->messages());

        // 2. Se falhar, faz todo o trabalho sujo de redirecionar
        if ($validador->fails()) {
            // Salva erros na sessão
            session_flash('errors', $validador->errors()->all());
            
            // Salva os dados antigos (exceto senha)
            if (isset($data['senha'])) {
                unset($data['senha']);
            }
            session_flash('old', $data);

            // Tchau! Volta para o formulário
            header('Location: ' . $redirectUrl);
            exit;
        }

        // 3. Se deu tudo certo, retorna os dados validados!
        return $data;
    }
}