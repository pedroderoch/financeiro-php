<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriarTabelaUsuarios extends AbstractMigration
{
    public function change(): void
    {
        // Cria a tabela 'usuarios'
        $table = $this->table('usuarios');
        
        $table->addColumn('nome', 'string', ['limit' => 255])
              
              // 'usuario' (login) - deve ser único
              ->addColumn('usuario', 'string', ['limit' => 100])
              ->addIndex(['usuario'], ['unique' => true])
              
              // 'email' - deve ser único
              ->addColumn('email', 'string', ['limit' => 255])
              ->addIndex(['email'], ['unique' => true])
              
              ->addColumn('senha', 'string', ['limit' => 255])
              
              // 'foto' pode ser nulo
              ->addColumn('foto', 'string', ['limit' => 255, 'null' => true])
              
              // 'nivel' tem um valor padrão
              ->addColumn('nivel', 'string', ['limit' => 50, 'default' => 'usuario'])
              
              // Cria automaticamente 'created_at' e 'updated_at'
              ->addTimestamps()
              
              ->create();
    }
}