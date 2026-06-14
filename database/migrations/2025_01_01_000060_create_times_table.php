<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelada_id')->constrained('peladas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('cor')->nullable();
            $table->unsignedTinyInteger('ordem')->default(1);
            $table->timestamps();
        });

        Schema::create('time_atleta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_id')->constrained('times')->cascadeOnDelete();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->boolean('e_goleiro')->default(false);
            $table->timestamps();

            $table->unique(['time_id', 'atleta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_atleta');
        Schema::dropIfExists('times');
    }
};
