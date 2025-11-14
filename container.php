<?php
// C:\financeiro\container.php

// Importa as classes que vamos usar
use DI\ContainerBuilder;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

// 1. Cria o "construtor" do nosso contêiner
$containerBuilder = new ContainerBuilder();

// 2. Adiciona as "definições" (as "receitas" de como construir as coisas)
$containerBuilder->addDefinitions([
    
    // 2a. Define como construir o Twig (Twig\Environment)
    Environment::class => function () {
        // A "receita" é exatamente o código que tínhamos no BaseController
        $loader = new FilesystemLoader(__DIR__ . '/views');
        
        $twig = new Environment($loader, [
             'cache' => false, 
             'debug' => true
        ]);

        $twig->addExtension(new DebugExtension());
        $twig->addFunction(new TwigFunction('session_has', 'session_has'));
        $twig->addFunction(new TwigFunction('session_get', 'session_get'));
        
        return $twig;
    },

]);

// 3. Constrói o contêiner e o retorna
return $containerBuilder->build();