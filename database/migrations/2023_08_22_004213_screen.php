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
        Schema::create('screen', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('table');
            $table->string('title');
            $table->string('icon');
            $table->string('description');
            $table->string('url');
            $table->timestamps();
        });

        $fieldConfigTemplate = <<<JSON
          {
            "name": "",
            "label": "",
            "type": "",
            "mask": "",
            "placeholder": "",
            "options": [],
            "default": "",
            "description": "",
            "required": false,
            "rules": [],
            "disabled": false,
            "visible": true,
            "searchUrl": "",
            "multiple": false,
            "useAjaxToLoadOptions": false
          }
JSON;
        Schema::create('screen_fields', function (Blueprint $blueprint) {

            $blueprint->id();
            $blueprint->foreignId('screen_id')->constrained('screen');
            $blueprint->json('config');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screen_fields');
        Schema::dropIfExists('screen');
    }
};
