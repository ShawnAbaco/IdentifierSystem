<?php
// database/migrations/2024_01_01_000002_create_species_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('common_name');
            $table->string('scientific_name')->nullable();
            $table->text('description');
            $table->json('characteristics')->nullable();
            $table->string('image_url')->nullable();
            $table->string('habitat')->nullable();
            $table->string('conservation_status')->nullable();
            $table->json('fun_facts')->nullable();
            $table->json('medicinal_uses')->nullable();
            $table->json('cultural_significance')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('species');
    }
};
