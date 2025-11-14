<?php

namespace App\Controller;

use Twig\Environment; // Importamos para usar no construtor


class HomeController extends BaseController
{
    /**
     * Precisamos de um construtor que receba o Twig
     * e o "repasse" para o construtor do BaseController (o "pai").
     */
    public function __construct(Environment $twig)
    {
        parent::__construct($twig); // parent:: é como se fosse super()
    }
    
    public function index(): void
    {
        $dados = [
            'titulo' => 'Bem-vindo ao Meu Projeto!',
            'descricao' => 'Esta página está sendo renderizada com DI e Twig.',
            'teste' => 'isso é pra ver se funciona do jeito que estou pensando'
        ];

        // O $this->render() continua funcionando!
        $this->render('home.html.twig', $dados);
    }
}