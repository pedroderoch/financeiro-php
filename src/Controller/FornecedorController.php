<?php

namespace App\Controller;

// 1. Importamos o nosso NOVO Model Eloquent
use App\Model\Fornecedor;
use App\Model\Situacao;
use Twig\Environment; 
use App\Request\FornecedorStoreRequest;
use App\Request\FornecedorUpdateRequest;

class FornecedorController extends BaseController
{

    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    public function index(): void {

        // $fornecedores = Fornecedor::all();

        $fornecedores = Fornecedor::naoExcluidos()->orderBy('id')->get();

        // dd($fornecedores->toArray());
    
        $dados = [
            'fornecedores' => $fornecedores
        ];
        
        $this->render('fornecedor_lista.html.twig', $dados);

    }

    public function create(): void{
        $this->render('fornecedor_form.html.twig', [
            'fornecedor' => null
        ]);
    }

    public function store(): void {
        $request = new FornecedorStoreRequest();

        $dadosValidados = $request->validate($_POST, '/fornecedores/cadastrar');

        try {
            Fornecedor::create($dadosValidados);
            session_flash('success', 'Fornecedor cadastrado com sucesso!');
            header('Location: /fornecedores');
            exit;
        } catch (\Exception $e) {
            // Erro de banco de dados (não de validação)
            error_log('Erro BD: ' . $e->getMessage());
            session_flash('errors', ['Erro interno ao salvar.']);
            header('Location: /fornecedores/cadastrar');
            exit;
        }
    }

    public function edit(array $params): void {
        $id = $params['id'];
        $fornecedor = Fornecedor::find($id);

        // dd($fornecedor->toArray());
        
        if(!$fornecedor){
            header('Location: /fornecedores');
            exit;
        }

        $this->render('fornecedor_form.html.twig', [
            'fornecedor' => $fornecedor
        ]);


    }

    public function update(array $params): void {
        
        $id = (int) $params['id'];
        $fornecedor = Fornecedor::find($id);

        if(!$fornecedor){
            header('Location: /fornecedores');
            exit();
        }

        $request = new FornecedorUpdateRequest();
        $dadosValidados = $request->validate($_POST, '/fornecedores/editar/' . $id);

        try {
            $fornecedor->update($dadosValidados);
            session_flash('success', 'Fornecedor atualizado com sucesso!');
            header('Location: /fornecedores');
            exit;
        } catch (\Exception $e) {
            error_log('Erro ao atualizar: ' . $e->getMessage());
            session_flash('errors', ['Erro interno ao atualizar.']);
            header('Location: /fornecedores/editar/' . $id);
            exit;
        }
            
    }

    public function destroy(array $params): void {
        
        $id = $params['id'];
        $fornecedor = Fornecedor::find($id);

        if ($fornecedor) {
            // $fornecedor->delete();
            $fornecedor->situacao_id = Situacao::EXCLUIDO;
            $fornecedor->save();
            session_flash('success', 'Fornecedor removido com sucesso!');
        } else {
            session_flash('errors', ['Fornecedor não encontrado.']);
        }

        header('Location: /fornecedores');
        exit;
    }

}    