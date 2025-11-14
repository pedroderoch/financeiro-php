<?php
// C:\financeiro\routes.php

/**
 * Este arquivo é o "mapa" de rotas.
 * Ele não executa nada, ele apenas DEFINE as rotas
 * e retorna o "despachante" (dispatcher) pronto para
 * quem o incluir (o index.php).
 */

// Importa as classes que vamos usar para definir as rotas
use App\Controller\HomeController;
use App\Controller\UsuarioController;
use App\Controller\ErrorController;
use App\Controller\LoginController; 

// A função simpleDispatcher é do FastRoute
return FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    
    // Rotas da Aplicação
    $r->addRoute('GET', '/', [HomeController::class, 'index']);

    //Usuarios
    $r->addRoute('GET', '/usuarios', [UsuarioController::class, 'list']);
    $r->addRoute('GET', '/usuario/{id:\d+}', [UsuarioController::class, 'show']);
    $r->addRoute('GET', '/usuarios/cadastrar', [UsuarioController::class, 'create']);
    $r->addRoute('POST', '/usuarios/criar', [UsuarioController::class, 'store']);
    $r->addRoute('GET', '/usuarios/editar/{id:\d+}', [UsuarioController::class, 'edit']);
    $r->addRoute('POST', '/usuarios/atualizar/{id:\d+}', [UsuarioController::class, 'update']);
    $r->addRoute('POST', '/usuarios/excluir/{id:\d+}', [UsuarioController::class, 'destroy']);

    // --- ROTAS DE AUTENTICAÇÃO ---
    $r->addRoute('GET', '/login', [LoginController::class, 'index']);
    $r->addRoute('POST', '/login', [LoginController::class, 'login']);
    $r->addRoute('GET', '/logout', [LoginController::class, 'logout']);

    // Rotas de teste
    $r->addRoute('GET', '/teste-add-usuario', [UsuarioController::class, 'testeAddUsuario']);
    $r->addRoute('GET', '/teste-update-usuario/{id:\d+}', [UsuarioController::class, 'testeUpdateUsuario']);
    $r->addRoute('GET', '/teste-remove-usuario/{id:\d+}', [UsuarioController::class, 'testeRemoveUsuario']);
});