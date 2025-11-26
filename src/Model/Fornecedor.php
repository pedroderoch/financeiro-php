<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Esta classe é o nosso "Model" Eloquent para a tabela 'usuarios'.
 * Ao herdar (extends) de Model, ela ganha super-poderes
 * como ::find(), ::all(), ->save(), ->delete(), etc.
 */
class Fornecedor extends Model
{
    protected $table = 'fornecedores';

    protected $fillable = [
        'nome',
        'descricao',
        'chave_pix',
        'foto'
    ];
}