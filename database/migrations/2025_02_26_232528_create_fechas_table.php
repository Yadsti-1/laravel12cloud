<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fechas', function (Blueprint $table) {
            $table->id();
            $table->string('categoria'); // Ejemplo: "Renta Grandes Contribuyentes"
            $table->string('mes'); // Mes de la obligación
            $table->string('concepto'); // Ejemplo: "Pago 1ra. cuota"
            $table->integer('ultimo_digito_nit'); // Último dígito del NIT
            $table->date('fecha'); // Fecha exacta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fechas');
    }
};
