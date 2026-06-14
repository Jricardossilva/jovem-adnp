<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atletas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('apelido')->nullable();
            $table->string('telefone', 20)->nullable();
            // 4 últimos dígitos do telefone, usados na verificação leve de acesso.
            $table->string('telefone_final', 4)->nullable()->index();
            $table->boolean('e_goleiro')->default(false);
            $table->unsignedTinyInteger('nivel')->default(3); // 1 a 5
            $table->string('status')->default('ativo');               // ativo|suspenso|inativo
            $table->string('situacao_cadastro')->default('pendente'); // pendente|aprovado|rejeitado
            $table->unsignedInteger('faltas_consecutivas')->default(0);
            $table->timestamp('aprovado_em')->nullable();
            $table->foreignId('aprovado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'situacao_cadastro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atletas');
    }
};
