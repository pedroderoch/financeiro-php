<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriarTabelaLancamentos extends AbstractMigration
{
    public function change(): void
    {
        // Cria a tabela com o nome correto
        $table = $this->table('lancamentos');
        
        $table->addColumn('descricao', 'string', ['limit' => 255])
              ->addColumn('valor', 'decimal', ['precision' => 10, 'scale' => 2])
              ->addColumn('valor_pago', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
              
              ->addColumn('data_vencimento', 'date')
              ->addColumn('data_pagamento', 'date', ['null' => true])
              
              // Tipo: 'receita' ou 'despesa'
              ->addColumn('tipo', 'enum', ['values' => ['receita', 'despesa']])
              
              // Status: 'pendente' ou 'pago'
              ->addColumn('status', 'enum', ['values' => ['pendente', 'pago'], 'default' => 'pendente'])
              ->addColumn('forma_pagamento', 'enum', ['values' => ['nao_informado', 'pix', 'boleto', 'transferencia'], 'default' => 'nao_informado'])
              
              ->addColumn('parcela_atual', 'integer', ['null' => true])
              ->addColumn('total_parcelas', 'integer', ['null' => true])
              ->addColumn('observacoes', 'text', ['null' => true])

              // CHAVES ESTRANGEIRAS (Colunas)
              // CORREÇÃO: Definimos 'signed' => false porque seus IDs são UNSIGNED
              ->addColumn('usuario_id', 'integer', ['signed' => false])
              ->addColumn('fornecedor_id', 'integer', ['signed' => false])
              
              ->addTimestamps()
              
              // RELACIONAMENTOS (Constraints)
              // Garante que usuario_id existe na tabela usuarios
              ->addForeignKey('usuario_id', 'usuarios', 'id', [
                  'delete'=> 'CASCADE',
                  'update'=> 'NO_ACTION'
              ])
              // Garante que fornecedor_id existe na tabela fornecedores
              ->addForeignKey('fornecedor_id', 'fornecedores', 'id', [
                  'delete'=> 'RESTRICT',
                  'update'=> 'NO_ACTION'
              ])
              
              ->create();
    }
}