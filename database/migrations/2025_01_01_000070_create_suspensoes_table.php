<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suspensoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->text('motivo');
            $table->date('inicio');
            $table->date('fim');
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['atleta_id', 'inicio', 'fim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suspensoes');
    }
};
