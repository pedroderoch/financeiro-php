<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterarFormaPagamentoParaVarchar extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('lancamentos');
        // Muda para string (VARCHAR 50) e permite NULL
        $table->changeColumn('forma_pagamento', 'string', ['limit' => 50, 'null' => true])
              ->save();
    }

    public function down(): void
    {
        // Se precisar voltar, reverte para ENUM (ajuste os valores conforme o antigo se necessário)
        $table = $this->table('lancamentos');
        $table->changeColumn('forma_pagamento', 'enum', [
            'values' => ['nao_informado', 'pix', 'boleto', 'transferencia'], 
            'null' => true
        ])->save();
    }
}