<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriarTabelaFornecedores extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // Cria a tabela 'fornecedores'
        $table = $this->table('fornecedores');
        
        $table->addColumn('nome', 'string', ['limit' => 255])
              ->addColumn('descricao', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('chave_pix', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('foto', 'string', ['limit' => 255, 'null' => true])
              // Cria automaticamente 'created_at' e 'updated_at'
              ->addTimestamps()
              ->create();
    }
}
