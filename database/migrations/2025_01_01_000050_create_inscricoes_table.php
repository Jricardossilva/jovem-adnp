<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->boolean('presente')->default(false);     // confirmado na checagem do dia
            $table->timestamp('confirmado_em')->nullable();  // momento da confirmação de presença
            $table->string('origem')->default('atleta');     // atleta|organizador
            $table->timestamps();

            $table->unique(['pelada_id', 'atleta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
