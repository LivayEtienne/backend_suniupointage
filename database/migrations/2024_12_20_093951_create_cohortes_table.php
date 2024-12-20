<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cohortes', function (Blueprint $table) {
            $table->id(); // ID unique de la cohorte
            $table->string('nom'); // Nom de la cohorte
            $table->string('code')->unique(); // Code unique de la cohorte
            $table->date('date_de_creation'); // Date de création
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cohortes');
    }
};
