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

        Schema::create('appointment', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string("about")->nullable();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('appointment_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->references('id')->on('appointment');
            $table->foreignId('patient_id')->references('id')->on('patient');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_patients');
        Schema::dropIfExists('appointment');
    }
};
