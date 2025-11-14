<?php

namespace App\Controller;

use App\Model\Usuario;
use Twig\Environment;

class LoginController extends BaseController
{
    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    /**
     * GET /login
     * Mostra o formulário de login.
     */
    public function index(): void
    {
        // Se o usuário já estiver logado, não faz sentido ver a tela de login.
        // Redireciona ele direto para a home ou lista de usuários.
        if (session_has('user_id')) {
            header('Location: /usuarios');
            exit;
        }

        $this->render('login.html.twig');
    }

    /**
     * POST /login
     * Processa a tentativa de login.
     */
    public function login(): void
    {
        $dados = $_POST;

        // 1. Busca o usuário no banco pelo "login" (campo 'usuario')
        // O método 'where' do Eloquent retorna uma QueryBuilder.
        // O 'first()' executa a query e pega o primeiro resultado (ou null).
        $usuario = Usuario::where('usuario', $dados['usuario'])->first();

        // 2. Verificação de Segurança
        // Precisamos checar DUAS coisas:
        // A) Se o usuário foi encontrado ($usuario existe?)
        // B) Se a senha digitada bate com o HASH no banco (password_verify)
        if ($usuario && password_verify($dados['senha'], $usuario->senha)) {
            
            // SUCESSO!
            
            // 3. "Logamos" o usuário salvando o ID dele na sessão.
            // Enquanto esse ID estiver na sessão, ele é considerado logado.
            $_SESSION['user_id'] = $usuario->id;
            $_SESSION['user_nome'] = $usuario->nome; // Opcional: guardar o nome para exibir na tela

            session_flash('success', 'Bem-vindo de volta, ' . $usuario->nome . '!');
            header('Location: /usuarios');
            exit;
        }

        // FALHA!
        
        // Se chegou aqui, ou o usuário não existe ou a senha está errada.
        // Por segurança, nunca dizemos QUAL dos dois errou. Dizemos "Credenciais inválidas".
        session_flash('error', 'Usuário ou senha incorretos.');
        
        // Mantém o nome de usuário preenchido para ele não ter que digitar de novo
        session_flash('old_usuario', $dados['usuario']);
        
        header('Location: /login');
        exit;
    }

    /**
     * GET /logout
     * Sai do sistema.
     */
    public function logout(): void
    {
        // Destroi todas as variáveis da sessão (limpa o carrinho)
        session_destroy();
        
        // Inicia uma nova sessão limpa apenas para poder mandar a msg de "Tchau"
        session_start(); 
        // (Nota: session_destroy mata tudo, então precisamos do start pra usar o flash na proxima pag)
        
        // Como destruímos a sessão antiga, usamos a $_SESSION direta aqui ou recriamos o helper.
        // Mas para simplificar, apenas redirecionamos.
        header('Location: /login');
        exit;
    }
}