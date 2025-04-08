<?php

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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->string('keywords')->nullable();
            $table->string('description')->nullable();
            $table->text('content');
            $table->string('slug')->nullable();
            $table->string('cover')->nullable();
            $table->json('setting')->nullable();
            $table->tinyInteger('status')->default(0);

            $table->unsignedTinyInteger('sort')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
