<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Esta classe é o nosso "Model" Eloquent para a tabela 'fornecedores'.
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
        'foto',
        'situacao_id'
    ];


    // --- RELACIONAMENTOS ---

    public function situacao()
    {
        return $this->belongsTo(Situacao::class, 'situacao_id');
    }

    // --- SCOPES (Filtros) ---

    /**
     * Filtra apenas os registos que NÃO estão excluídos.
     * Inclui Ativos e Inativos.
     * Uso: Usuario::naoExcluidos()->get();
     */
    public function scopeNaoExcluidos($query)
    {
        return $query->where('situacao_id', '!=', Situacao::EXCLUIDO);
    }
}