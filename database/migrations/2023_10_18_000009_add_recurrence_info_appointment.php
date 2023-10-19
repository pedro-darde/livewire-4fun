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
       Schema::table('appointment', function (Blueprint $table) {
            $table->boolean('is_recurrence')->default(false);
            $table->integer('recurrence_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('appointment', ['is_recurrence', 'recurrence_type']);
    }
};
