<?php
// A SESSÃO DEVE SER A PRIMEIRA COISA A SER INICIADA
session_start();

// 1. Inicialização (Composer)
require_once __DIR__ . '/../vendor/autoload.php';

// 2. "Ligar" o Eloquent
require_once __DIR__ . '/../bootstrap.php'; 

// 3. Importar o Contêiner de Injeção de Dependência
$container = require __DIR__ . '/../container.php';

// 4. Importar o Controller de Erro (para o switch)
use App\Controller\ErrorController;

// 5. CARREGAR O "MAPA DE ROTAS"
// O $dispatcher agora é criado pelo routes.php
$dispatcher = require __DIR__ . '/../routes.php';

// 6. Processamento da Requisição 
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// ============================================================
// 🛡️ SISTEMA DE PROTEÇÃO DE ROTAS (MIDDLEWARE MANUAL)
// ============================================================

// 1. Definimos quais rotas são PÚBLICAS (não precisam de login)
// Se a URL for uma dessas, deixamos passar.
$rotasPublicas = [
    '/login'
];

// 2. Verificamos:
// - A rota atual NÃO está na lista de públicas?
// - E a sessão do usuário NÃO existe (não está logado)?
if (!in_array($uri, $rotasPublicas) && !isset($_SESSION['user_id'])) {
    
    // Se caiu aqui, é um intruso!
    // Redireciona para o login.
    header('Location: /login');
    exit;
}
// ============================================================

// 7. Tratamento do Resultado 
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $controller = $container->get(ErrorController::class);
        $controller->notFound();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $controller = $container->get(ErrorController::class);
        $controller->methodNotAllowed();
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        
        [$class, $method] = $handler;

        // ============================================================
        // 🛡️ VERIFICADOR DE TOKEN CSRF (Middleware Manual)
        // ============================================================

        // Verificamos se é uma requisição que MUDA dados (POST)
        if ($httpMethod === 'POST') {
            // Pegamos o token enviado pelo formulário
            $token = $_POST['csrf_token'] ?? '';
            // Usamos nossa função helper para validar
            if (!validate_csrf_token($token)) {
                // Se o token for inválido, paramos tudo.
                // 419 é o código HTTP para "Authentication Timeout"
                // (usado pelo Laravel para falha de CSRF)
                $controller = $container->get(ErrorController::class);
                $controller->csrfError();
                exit;
            }
        }
        // ============================================================

        $controller = $container->get($class);
        
        $controller->$method($vars);
        break;
}