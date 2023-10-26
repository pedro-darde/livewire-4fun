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
        Schema::table('patient', function (Blueprint $table) {
            $table->date('first_contact_date')->nullable();
            $table->string('initial_demand', 500)->nullable();
            $table->string('initial_diagnosis', 500)->nullable();
            $table->string("objective", 500)->nullable();
            $table->string("emergency_contact")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('patient',['first_contact_date', 'initial_demand', 'initial_diagnosis', 'objective', 'emergency_contact']);
    }
};
