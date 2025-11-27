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
     
        $lancamentos = Lancamento::all();

        $dados = [
            'lancamentos' => $lancamentos
        ];
        
        $this->render('lancamentos_lista.html.twig', $dados);
    }

    public function create(): void{

        // Busca todos os fornecedores para preencher o <select> no formulário
        $fornecedores = Fornecedor::all();

        $this->render('lancamento_form.html.twig', [
            'lancamento' => null,
            'fornecedores' => $fornecedores
        ]);
    }

    public function store(): void {
        $request = new LancamentoStoreRequest();

        $dadosValidados = $request->validate($_POST, '/lancamentos/cadastrar');

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
            header('Location: /lancamentos');
            exit;
        }

        // Busca todos os fornecedores para preencher o <select> no formulário
        $fornecedores = Fornecedor::all();

        $this->render('lancamento_form.html.twig', [
            'lancamento' => $lancamento,
            'fornecedores' => $fornecedores
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