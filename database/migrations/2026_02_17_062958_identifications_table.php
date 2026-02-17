<?php
// database/migrations/2024_01_01_000003_create_identifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('identifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('species_id')->nullable()->constrained()->onDelete('set null');
            $table->string('identified_as');
            $table->decimal('confidence', 5, 4);
            $table->json('all_predictions')->nullable();
            $table->string('image_path');
            $table->text('user_notes')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('identifications');
    }
};
