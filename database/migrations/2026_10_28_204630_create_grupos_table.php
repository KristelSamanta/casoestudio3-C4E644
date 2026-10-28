<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('numero_ciclo');
            $table->unsignedInteger('ano');
            $table->unsignedInteger('numero_grupo');
            $table->boolean('abierto');
            $table->foreignId('curso_id')->constrained('cursos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
