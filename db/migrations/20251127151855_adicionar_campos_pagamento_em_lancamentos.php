<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdicionarCamposPagamentoEmLancamentos extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('lancamentos');
        
        $table->addColumn('valor_pago', 'decimal', [
                'precision' => 10, 
                'scale' => 2, 
                'null' => true, // Pode ser nulo se ainda não foi pago
                'after' => 'valor' // Posiciona depois da coluna 'valor' original
            ])
            ->addColumn('forma_pagamento', 'string', [
                'limit' => 50, 
                'null' => true,
                'after' => 'status' // Posiciona depois da coluna 'status'
            ])
            ->update(); // Aplica as alterações
    }
}