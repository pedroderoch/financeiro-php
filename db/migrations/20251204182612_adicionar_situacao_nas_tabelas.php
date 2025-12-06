<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AdicionarSituacaoNasTabelas extends AbstractMigration
{
    public function change(): void
    {
        // Tabelas que vão receber o campo
        $tabelas = ['usuarios', 'fornecedores', 'lancamentos'];

        foreach ($tabelas as $nomeTabela) {
            $table = $this->table($nomeTabela);
            
            // Verifica se a coluna já existe antes de adicionar
            if (!$table->hasColumn('situacao_id')) {
                
                // Adiciona a coluna situacao_id
                // CORREÇÃO: 'signed' => false para bater com o padrão Unsigned das tabelas existentes
                $table->addColumn('situacao_id', 'integer', [
                    'signed' => false, // <--- ADICIONADO NOVAMENTE
                    'default' => 1,    // ID 1 = Ativo
                    'after' => 'id'
                ]);

                // Cria a chave estrangeira
                $table->addForeignKey('situacao_id', 'situacoes', 'id', [
                    'delete' => 'RESTRICT', 
                    'update' => 'NO_ACTION'
                ]);

                $table->update();
            }
        }
    }
}