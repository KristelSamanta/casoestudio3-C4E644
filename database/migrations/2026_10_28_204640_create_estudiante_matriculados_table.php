<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiante_matriculados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('estudiante_id')->constrained('estudiantes');
            $table->decimal('nota', 5, 2)->nullable();
            $table->unique(['grupo_id', 'estudiante_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante_matriculados');
    }
};
