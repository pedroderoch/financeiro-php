<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriarTabelaSituacoes extends AbstractMigration
{
    public function up(): void
    {
        // 1. Cria a tabela
        $table = $this->table('situacoes');
        $table->addColumn('sigla', 'char', ['limit' => 1]) // A, I, E
              ->addColumn('descricao', 'string', ['limit' => 50]) // Ativo, Inativo, Excluído
              ->addIndex(['sigla'], ['unique' => true])
              ->create();

        // 2. Insere os dados padrão (CORREÇÃO AQUI)
        // Usamos o método insert() do próprio objeto tabela
        if ($this->isMigratingUp()) {
            $rows = [
                ['id' => 1, 'sigla' => 'A', 'descricao' => 'Ativo'],
                ['id' => 2, 'sigla' => 'I', 'descricao' => 'Inativo'],
                ['id' => 3, 'sigla' => 'E', 'descricao' => 'Excluído']
            ];

            // Pega a referência da tabela novamente e salva os dados
            $this->table('situacoes')->insert($rows)->saveData();
        }
    }

    public function down(): void
    {
        $this->table('situacoes')->drop()->save();
    }
}