<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_block_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('page_blocks')->cascadeOnDelete();
            $table->text('template')->nullable();
            $table->text('scripts')->nullable();
            $table->text('stylesheets')->nullable();
            $table->json('styles')->nullable();
            $table->json('schema_values')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_block_values');
    }
};
