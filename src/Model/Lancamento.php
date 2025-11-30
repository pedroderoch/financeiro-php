<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    protected $table = 'lancamentos';

    protected $fillable = [
        'descricao',
        'valor',
        'valor_pago',
        'data_vencimento',
        'data_pagamento',
        'tipo',           // 'receita' ou 'despesa'
        'status',         // 'pendente' ou 'pago'
        'forma_pagamento',
        'parcela_atual',
        'total_parcelas',
        'observacoes',
        'usuario_id',     // Chave estrangeira
        'fornecedor_id'   // Chave estrangeira
    ];

    // --- MUTATORS (A Correção Definitiva) ---

    /**
     * Interceta a Data de Pagamento antes de salvar.
     * Se vier vazio (""), converte para NULL.
     */
    public function setDataPagamentoAttribute($value)
    {
        $this->attributes['data_pagamento'] = empty($value) ? null : $value;
    }

    /**
     * Fazemos o mesmo para o Valor Pago, para evitar salvar 0.00 se estiver vazio
     */
    public function setValorPagoAttribute($value)
    {
        $this->attributes['valor_pago'] = (empty($value) && $value !== '0') ? null : $value;
    }

    /**
     * E para a Forma de Pagamento também
     */
    public function setFormaPagamentoAttribute($value)
    {
        $this->attributes['forma_pagamento'] = empty($value) ? null : $value;
    }


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