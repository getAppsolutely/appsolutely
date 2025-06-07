<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('page_block_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('block_id')->constrained('page_blocks')->cascadeOnDelete();
            $table->string('type');
            $table->string('template')->nullable();
            $table->text('scripts')->nullable();
            $table->text('stylesheets')->nullable();
            $table->json('styles')->nullable();
            $table->json('parameter_values')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->string('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_block_settings');
    }
};
