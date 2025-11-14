<?php

namespace App\Model;

// Importamos a classe "Model" base do Eloquent
use Illuminate\Database\Eloquent\Model;

/**
 * Esta classe é o nosso "Model" Eloquent para a tabela 'usuarios'.
 * Ao herdar (extends) de Model, ela ganha super-poderes
 * como ::find(), ::all(), ->save(), ->delete(), etc.
 */
class Usuario extends Model
{
    protected $table = 'usuarios';

    // ATUALIZADO para usar 'usuario' e 'senha'
    protected $fillable = [
        'nome',
        'usuario', // <-- MUDOU DE 'username'
        'email',
        'senha', // <-- MUDOU DE 'password'
        'foto',
        'nivel' // (Se você adicionou a coluna 'nivel' também)
    ];

    /**
     * ATUALIZADO: Mutator MÁGICO para a coluna 'senha'
     * O nome do método muda de setPasswordAttribute para setSenhaAttribute
     */
    public function setSenhaAttribute(string $value): void
    {
        // O atributo no array 'attributes' também muda para 'senha'
        $this->attributes['senha'] = password_hash($value, PASSWORD_DEFAULT);
    }
}