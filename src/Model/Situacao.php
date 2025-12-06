<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Esta classe é o nosso "Model" Eloquent para a tabela 'usuarios'.
 * Ao herdar (extends) de Model, ela ganha super-poderes
 * como ::find(), ::all(), ->save(), ->delete(), etc.
 */
class Situacao extends Model
{
    protected $table = 'situacoes';

    public $timestamps = false;

    protected $fillable = ['sigla', 'descricao'];

    // Constantes para usar no código (Ex: Situacao::ATIVO)
    const ATIVO = 1;
    const INATIVO = 2;
    const EXCLUIDO = 3;
}