<?php

namespace App\Controller;

// 1. Importamos o nosso NOVO Model Eloquent
use App\Model\Usuario;
use Twig\Environment; 

class UsuarioController extends BaseController
{

    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    /**
     * Este método MOSTRA a página com o formulário de cadastro.
     * (Responde à rota GET /usuarios/cadastrar)
     */
    public function create(): void
    {
        // 1. Renderiza o formulário
        // 2. Passa uma variável 'usuario' vazia (ou null)
        //    para o formulário saber que é o modo "Criar"
        $this->render('usuario_form.html.twig', [
            'usuario' => null
        ]);
    }

    /**
     * Este método RECEBE os dados do formulário (via POST)
     * e os salva no banco de dados.
     * (Responde à rota POST /usuarios/criar)
     */
    public function store(): void
    {
        $dadosDoFormulario = $_POST;

        // 1. As Regras (como antes)
        $regras = [
            'nome' => 'required|min:3',
            'usuario' => 'required|unique:usuarios,usuario',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|min:6',
            'nivel' => 'required'
        ];

        // 2. !!!!! AQUI ESTÁ A SOLUÇÃO !!!!!
        // Criamos nossas próprias mensagens em português.
        // A sintaxe é 'nome_do_campo.nome_da_regra'
        $mensagens = [
            // Regras 'required'
            'nome.required' => 'O campo Nome é obrigatório.',
            'usuario.required' => 'O campo Usuário (login) é obrigatório.',
            'email.required' => 'O campo E-mail é obrigatório.',
            'senha.required' => 'O campo Senha é obrigatório.',
            'nivel.required' => 'O campo Nível é obrigatório.',
            
            // Regras 'min'
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            
            // Regras 'unique'
            'usuario.unique' => 'Este nome de usuário (login) já está em uso.',
            'email.unique' => 'Este endereço de e-mail já está em uso.',
            
            // Regras de formato
            'email.email' => 'Por favor, insira um formato de e-mail válido.'
        ];

        // 3. Crie o validador, passando as $regras E as $mensagens
        $validador = validator($dadosDoFormulario, $regras, $mensagens);

        // 4. Verifique se a validação falhou (o resto é igual)
        if ($validador->fails()) {
            session_flash('errors', $validador->errors()->all());
            unset($dadosDoFormulario['senha']);
            session_flash('old', $dadosDoFormulario);
            header('Location: /usuarios/cadastrar');
            exit;
        }

        // 5. Se a validação passou, continue...
        try {
            Usuario::create($dadosDoFormulario);
            session_flash('success', 'Usuário cadastrado com sucesso!');
            header('Location: /usuarios');
            exit;
        } catch (\Exception $e) {
            error_log('Erro ao salvar usuário: ' . $e->getMessage());
            session_flash('errors', ['Ocorreu um erro inesperado ao salvar.']);
            session_flash('old', $dadosDoFormulario);
            header('Location: /usuarios/cadastrar');
            exit;
        }
    }

    /**
     * (CONVENÇÃO DE MERCADO)
     * Este método MOSTRA o formulário de edição PREENCHIDO.
     * (Responde à rota GET /usuarios/editar/{id})
     */
    public function edit(array $params): void
    {
        // 1. Pega o ID da URL
        $id = $params['id'];
        
        // 2. Busca o usuário no banco
        $usuario = Usuario::find($id);

        if (!$usuario) {
            // Se não achar, redireciona para a lista
            header('Location: /usuarios');
            exit;
        }
        
        // 3. Renderiza o MESMO formulário, mas agora passando
        //    o objeto 'usuario' com os dados dele.
        $this->render('usuario_form.html.twig', [
            'usuario' => $usuario
        ]);
    }

/**
     * Este método RECEBE os dados do formulário de EDIÇÃO (via POST)
     * e ATUALIZA o usuário no banco.
     */
    public function update(array $params): void
    {
        $id = $params['id'];
        $dadosDoFormulario = $_POST;

        // 1. Encontra o usuário PRIMEIRO (precisamos saber se ele existe)
        $usuario = Usuario::find($id);

        if (!$usuario) {
            header('Location: /usuarios');
            exit;
        }

        // 2. REGRAS DE VALIDAÇÃO ADAPTADAS
        $regras = [
            'nome' => 'required|min:3',
            'usuario' => 'required|unique:usuarios,usuario,' . $id, // <-- IGNORA O ID ATUAL
            'email' => 'required|email|unique:usuarios,email,' . $id, // <-- IGNORA O ID ATUAL
            'nivel' => 'required',
            // A senha não é 'required' aqui. Só validamos SE ela for enviada.
            'senha' => 'nullable|min:6' 
        ];

        $mensagens = [
            'nome.required' => 'O campo Nome é obrigatório.',
            'usuario.required' => 'O campo Usuário é obrigatório.',
            'usuario.unique' => 'Este usuário já está em uso por outra pessoa.',
            'email.required' => 'O campo E-mail é obrigatório.',
            'email.email' => 'Email inválido.',
            'email.unique' => 'Este e-mail já está em uso por outra pessoa.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.'
        ];

        // 3. Valida
        $validador = validator($dadosDoFormulario, $regras, $mensagens);

        if ($validador->fails()) {
            session_flash('errors', $validador->errors()->all());
            
            // Limpa a senha dos dados antigos por segurança
            unset($dadosDoFormulario['senha']);
            
            // Retorna os dados para preencher o formulário
            session_flash('old', $dadosDoFormulario);
            
            header('Location: /usuarios/editar/' . $id);
            exit;
        }

        // 4. LÓGICA DA SENHA (Igual a antes)
        // Se a senha estiver vazia, removemos do array para não apagar a senha antiga
        if (empty($dadosDoFormulario['senha'])) {
            unset($dadosDoFormulario['senha']);
        }

        // 5. Atualiza
        try {
            $usuario->update($dadosDoFormulario);
            session_flash('success', 'Usuário atualizado com sucesso!');
            header('Location: /usuarios');
            exit;
        } catch (\Exception $e) {
            error_log('Erro ao atualizar: ' . $e->getMessage());
            session_flash('errors', ['Erro inesperado ao atualizar.']);
            header('Location: /usuarios/editar/' . $id);
            exit;
        }
    }

    public function destroy(array $params): void
    {
        // 1. Pega o ID da URL (exatamente como no update)
        $id = $params['id'];

        // 2. Encontra o usuário
        $usuario = Usuario::find($id);

        // 3. Se o usuário existir, o Eloquent o apaga
        if ($usuario) {
            $usuario->delete();
        }

        // 4. Redireciona de volta para a lista em qualquer caso
        header('Location: /usuarios');
        exit;
    }

    /**
     * Gerencia a rota '/usuarios' (Listar)
     */
    public function list(): void
    {
        // 2. Veja como a sintaxe mudou!
        // Adeus Repositório, adeus EntityManager...
        // ::all() é o Eloquent a dizer "SELECT * FROM usuarios"
        $usuariosReais = Usuario::all();

        // dd($usuariosReais->toArray());
        
        $dados = [
            'usuarios' => $usuariosReais
        ];
        
        // 3. Renderiza a view (o .twig não muda nada!)
        $this->render('usuarios_lista.html.twig', $dados);
    }

    /**
     * Gerencia a rota '/usuario/{id}' (Ver um)
     */
    public function show(array $params): void
    {
        $userId = $params['id'] ?? 0;
        
        // ::find() é o Eloquent a dizer "SELECT * FROM usuarios WHERE id = ?"
        $usuario = Usuario::find($userId);

        if (!$usuario) {
            $this->render('usuario_show.html.twig', ['error' => 'Usuário não encontrado']);
            return;
        }
        
        // Passa o objeto usuário completo para o Twig
        $this->render('usuario_show.html.twig', ['usuario' => $usuario]);
    }
    
    /**
     * Rota de teste para ADICIONAR um usuário (Create)
     */
    public function testeAddUsuario(): void
    {
        Usuario::query()->delete();
        // Usamos o método ::create() (Mass Assignment)
        // Isto só funciona porque definimos $fillable no Model
        $novoUsuario = Usuario::create([
            'nome' => 'Pedro Henrique',
            'usuario' => 'pedro_rocha',
            'email' => 'pedro.deroch@gmail.com',
            'senha' => '123456', 
            'nivel' => 'administrador'
        ]);

        echo "Usuário comum '{$novoUsuario->nome}' (nível: {$novoUsuario->nivel}) criado!<br>";
        echo '<br><a href="/usuarios">Ver a lista</a>';
    }
    
    /**
     * Rota de teste para ATUALIZAR um usuário (Update)
     */
    public function testeUpdateUsuario(array $params): void
    {
        $userId = $params['id'];
        
        // 1. Encontramos o usuário
        $usuario = Usuario::find($userId);

        if (!$usuario) {
            echo "Usuário com ID {$userId} não encontrado!";
            return;
        }

        // 2. Alteramos as propriedades do objeto (como no Doctrine)
        $usuario->nome = 'Kaio Jorge Matador Atualizado';
        
        // 3. O próprio objeto sabe como se salvar!
        // (Isto faz o "UPDATE ... WHERE id = ?")
        $usuario->save();

        echo "Usuário '{$usuario->nome}' atualizado com sucesso!";
        echo '<br><a href="/usuarios">Ver a lista</a>';
    }
    
    /**
     * Rota de teste para APAGAR um usuário (Delete)
     */
    public function testeRemoveUsuario(array $params): void
    {
        $userId = $params['id'];
        
        // 1. Encontramos o usuário
        $usuario = Usuario::find($userId);

        if (!$usuario) {
            echo "Usuário com ID {$userId} não encontrado!";
            return;
        }

        $nomeAntigo = $usuario->nome;
        
        // 2. O próprio objeto sabe como se apagar!
        // (Isto faz o "DELETE FROM ... WHERE id = ?")
        $usuario->delete();

        echo "Usuário '{$nomeAntigo}' foi removido com sucesso!";
        echo '<br><a href="/usuarios">Ver a lista</a>';
    }
}