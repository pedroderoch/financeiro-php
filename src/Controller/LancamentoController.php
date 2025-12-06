<?php

namespace App\Controller;

// 1. Importamos o nosso NOVO Model Eloquent
use App\Model\Lancamento;
use App\Model\Fornecedor;
use Twig\Environment; 
use App\Request\LancamentoStoreRequest;
use App\Request\LancamentoUpdateRequest;

class LancamentoController extends BaseController
{

    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    public function index(): void {
     
        // Lista todos (sem filtro de excluídos por enquanto), ordenado por data
        $lancamentos = Lancamento::with('fornecedor')
            ->orderBy('data_vencimento', 'desc')
            ->get();

        // --- CÁLCULOS DOS TOTAIS ---
        // Usamos o Eloquent para somar a coluna 'valor' filtrando pelo tipo
        $totalReceitas = Lancamento::where('tipo', 'receita')->sum('valor');
        $totalDespesas = Lancamento::where('tipo', 'despesa')->sum('valor');
        $saldoTotal = $totalReceitas - $totalDespesas;

        $this->render('lancamentos_lista.html.twig', [
            'lancamentos' => $lancamentos,
            'total_receitas' => $totalReceitas,
            'total_despesas' => $totalDespesas,
            'saldo_total' => $saldoTotal
        ]);
    }

    public function create(): void{

        // Busca todos os fornecedores para preencher o <select> no formulário
        $fornecedores = Fornecedor::orderBy('nome', 'asc')->get();

        $this->render('lancamento_form.html.twig', [
            'lancamento' => null,
            'fornecedores' => $fornecedores,
            'formas_pagamento' => Lancamento::FORMAS_PAGAMENTO
        ]);
    }

    public function store(): void {
        $request = new LancamentoStoreRequest();

        $dadosValidados = $request->validate($_POST, '/lancamentos/cadastrar');

        // Forçar NULL se estiver vazio ---
        if (empty($dadosValidados['data_pagamento'])) {
            $dadosValidados['data_pagamento'] = null;
        }
        if (empty($dadosValidados['valor_pago'])) {
            $dadosValidados['valor_pago'] = null;
        }

        try {
            // Adiciona o ID do utilizador logado aos dados validados
            $dadosValidados['usuario_id'] = $_SESSION['user_id'] ?? null;

            Lancamento::create($dadosValidados);

            session_flash('success', 'Lançamento cadastrado com sucesso!');
            header('Location: /lancamentos');
            exit;
        } catch (\Exception $e) {
            // Erro de banco de dados (não de validação)
            error_log('Erro BD: ' . $e->getMessage());
            session_flash('errors', ['Erro interno ao salvar.']);
            header('Location: /lancamentos/cadastrar');
            exit;
        }
    }

    public function edit(array $params): void {

        $id = $params['id'];
        $lancamento = Lancamento::find($id);

        // dd($fornecedor->toArray());
        
        if(!$lancamento){
            session_flash('errors', ['Lançamento não encontrado.']);
            header('Location: /lancamentos');
            exit;
        }

        // Busca todos os fornecedores para preencher o <select> no formulário
        $fornecedores = Fornecedor::all();

        $this->render('lancamento_form.html.twig', [
            'lancamento' => $lancamento,
            'fornecedores' => $fornecedores,
            'formas_pagamento' => Lancamento::FORMAS_PAGAMENTO
        ]);


    }

    public function update(array $params): void {
        
        $id = (int) $params['id'];
        $lancamento = Lancamento::find($id);

        if(!$lancamento){
            header('Location: /lancamentos');
            exit();
        }

        $request = new LancamentoUpdateRequest();
        $dadosValidados = $request->validate($_POST, '/lancamentos/editar/' . $id);
        
        // Forçar NULL se estiver vazio ---
        if (empty($dadosValidados['data_pagamento'])) {
            $dadosValidados['data_pagamento'] = null;
        }
        if (empty($dadosValidados['valor_pago'])) {
            $dadosValidados['valor_pago'] = null;
        }

        // dd($dadosValidados);

        try {
            $lancamento->update($dadosValidados);
            session_flash('success', 'Lançamento atualizado com sucesso!');
            header('Location: /lancamentos');
            exit;
        } catch (\Exception $e) {
            error_log('Erro ao atualizar: ' . $e->getMessage());
            session_flash('errors', ['Erro interno ao atualizar.']);
            header('Location: /lancamentos/editar/' . $id);
            exit;
        } 
    }

    public function destroy(array $params): void {
        
        $id = $params['id'];
        $lancamento = Lancamento::find($id);

        if ($lancamento) {
            $lancamento->delete();
        }

        header('Location: /lancamentos');
        exit;
    }

}    