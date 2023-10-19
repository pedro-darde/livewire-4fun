<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_supplied', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::unprepared('INSERT INTO service_supplied (name, description, created_at, updated_at) VALUES ("Avaliação Psicológica", "", NOW(), NOW())');

        Schema::table('appointment', function (Blueprint $table) {
            $table->unsignedBigInteger('id_service_supplied')->nullable();
            $table->foreign("id_service_supplied")
                ->references('id')
                ->on('service_supplied')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('id_service_supplied');
        });
        Schema::dropIfExists('service_supplied');
    }
};
