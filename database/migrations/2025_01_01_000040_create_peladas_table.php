<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peladas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recorrencia_id')->nullable()->constrained('recorrencias')->nullOnDelete();
            $table->foreignId('local_id')->constrained('locais')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('codigo', 8)->unique();
            $table->string('modalidade')->default('futsal');
            $table->unsignedTinyInteger('jogadores_por_time')->default(4);
            $table->boolean('com_goleiro')->default(true);
            $table->string('metodo_sorteio')->default('aleatorio');
            $table->date('data');
            $table->time('horario');
            $table->string('status')->default('agendada'); // agendada|aberta|fechada|encerrada
            $table->boolean('exige_verificacao_telefone')->default(true);
            $table->unsignedSmallInteger('max_atletas')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('sorteio_realizado_em')->nullable();
            $table->timestamp('encerrada_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peladas');
    }
};
