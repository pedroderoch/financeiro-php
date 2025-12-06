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
        'usuario',
        'email',
        'senha', 
        'foto',
        'nivel',
        'situacao_id'
    ];

    /**
     * Mutator para a senha (criptografia automática)
     */
    public function setSenhaAttribute(string $value): void
    {
        $this->attributes['senha'] = password_hash($value, PASSWORD_DEFAULT);
    }

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