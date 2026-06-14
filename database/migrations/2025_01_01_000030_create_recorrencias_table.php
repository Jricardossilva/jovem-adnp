<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recorrencias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('local_id')->constrained('locais')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('modalidade')->default('futsal');     // futsal|society
            $table->unsignedTinyInteger('jogadores_por_time')->default(4);
            $table->boolean('com_goleiro')->default(true);
            $table->string('metodo_sorteio')->default('aleatorio'); // aleatorio|balanceado
            $table->string('frequencia')->default('semanal');    // semanal|quinzenal|mensal
            $table->unsignedTinyInteger('dia_semana')->nullable(); // 0=domingo ... 6=sabado
            $table->time('horario');
            $table->boolean('exige_verificacao_telefone')->default(true);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recorrencias');
    }
};
