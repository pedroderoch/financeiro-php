<?php

namespace App\Controller;

// 1. Importamos o nosso NOVO Model Eloquent
use App\Model\Usuario;
use Twig\Environment; 
use App\Request\UsuarioStoreRequest;
use App\Request\UsuarioUpdateRequest;

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

    public function store(): void
    {
        // 1. Instancia a classe de validação
        $request = new UsuarioStoreRequest();

        // 2. Manda validar! 
        // Se der erro, ele redireciona SOZINHO lá dentro.
        // Se passar, ele devolve os dados limpos aqui.
        $dadosValidados = $request->validate($_POST, '/usuarios/cadastrar');

        // 3. Se chegou aqui, é SUCESSO. Só salvar.
        try {
            Usuario::create($dadosValidados);
            session_flash('success', 'Usuário cadastrado com sucesso!');
            header('Location: /usuarios');
            exit;
        } catch (\Exception $e) {
            // Erro de banco de dados (não de validação)
            error_log('Erro BD: ' . $e->getMessage());
            session_flash('errors', ['Erro interno ao salvar.']);
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

    public function update(array $params): void
    {
        // 1. Pegamos o ID da rota primeiro
        $id = (int) $params['id'];

        // 2. Encontramos o usuário no banco (Fundamental para o Eloquent saber QUEM atualizar)
        $usuario = Usuario::find($id);

        if (!$usuario) {
            header('Location: /usuarios');
            exit;
        }

        // 3. CORREÇÃO 1: Instanciamos a Request PASSANDO O ID
        // Isso permite que a regra 'unique' ignore este usuário específico
        $request = new UsuarioUpdateRequest($id);

        // 4. CORREÇÃO 2: A URL de erro precisa ter o ID
        // Se falhar, volta para: /usuarios/editar/1
        $dadosValidados = $request->validate($_POST, '/usuarios/editar/' . $id);

        // 5. Lógica da Senha (Opcional no Update)
        // Se a senha veio vazia, removemos do array para não sobrescrever com vazio
        if (empty($dadosValidados['senha'])) {
            unset($dadosValidados['senha']);
        }

        try {
            // 6. CORREÇÃO 3: Atualizamos a INSTÂNCIA ($usuario), não a classe estática
            $usuario->update($dadosValidados);
            
            session_flash('success', 'Usuário atualizado com sucesso!');
            header('Location: /usuarios');
            exit;

        } catch (\Exception $e) {
            error_log('Erro BD: ' . $e->getMessage());
            session_flash('errors', ['Erro interno ao atualizar dados.']);
            
            // Volta para o formulário com o ID correto
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

        echoooo "erro";
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