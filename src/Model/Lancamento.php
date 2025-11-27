<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    protected $table = 'lancamentos';

    protected $fillable = [
        'descricao',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'tipo',           // 'receita' ou 'despesa'
        'status',         // 'pendente' ou 'pago'
        'parcela_atual',
        'total_parcelas',
        'observacoes',
        'usuario_id',     // Chave estrangeira
        'fornecedor_id'   // Chave estrangeira
    ];

    // --- RELACIONAMENTOS ---

    /**
     * Um Lançamento "Pertence A" (Belongs To) um Usuário.
     * Isso permite: $lancamento->usuario->nome
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Um Lançamento "Pertence A" um Fornecedor.
     * Isso permite: $lancamento->fornecedor->nome
     */
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }
    
    // --- ACESSORS (Opcional, mas útil) ---
    
    /**
     * Formata o valor para dinheiro (R$) automaticamente
     * Uso: $lancamento->valor_formatado
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }
}