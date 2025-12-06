<?php

namespace App\Request;

use App\Model\Lancamento; // <--- Importante: Importar o Model

class LancamentoStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        $formasValidas = implode(',', array_keys(Lancamento::FORMAS_PAGAMENTO));

        return [
            'descricao'       => 'required|string|max:255',
            'valor'           => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            
            // Valida se o ID existe na tabela 'fornecedores'
            'fornecedor_id'   => 'required|exists:fornecedores,id',
            
            // Garante que só aceitamos os valores permitidos pelo ENUM do banco
            'tipo'            => 'required|in:receita,despesa',
            'status'          => 'required|in:pendente,pago',
            
            // Campos de Pagamento (Opcionais)
            'valor_pago'      => 'nullable|numeric|min:0',
            'forma_pagamento' => 'nullable|in:' . $formasValidas,
            'data_pagamento'  => 'nullable|date',
            
            // Parcelamento e Obs
            'parcela_atual'   => 'nullable|integer|min:1',
            'total_parcelas'  => 'nullable|integer|min:1',
            'observacoes'     => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'descricao.required'       => 'A descrição é obrigatória.',
            'valor.required'           => 'O valor é obrigatório e deve ser numérico.',
            'valor.min'                => 'O valor deve ser maior que zero.',
            'data_vencimento.required' => 'A data de vencimento é obrigatória.',
            
            'fornecedor_id.required'   => 'Selecione um Fornecedor/Favorecido.',
            'fornecedor_id.exists'     => 'O fornecedor selecionado não é válido.',
            
            'tipo.required'            => 'O tipo (Receita/Despesa) é obrigatório.',
            'tipo.in'                  => 'O tipo selecionado é inválido.',
            
            'status.required'          => 'A situação é obrigatória.',
            'status.in'                => 'A situação selecionada é inválida.',
            
            'forma_pagamento.in'       => 'A forma de pagamento selecionada é inválida.'
        ];
    }
}