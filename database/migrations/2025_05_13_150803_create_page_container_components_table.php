<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('page_container_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_container_id')->constrained()->onDelete('cascade');
            $table->string('component_name');
            $table->text('html');
            $table->string('layout')->nullable();
            $table->string('style')->nullable();
            $table->json('config')->nullable();
            $table->tinyInteger('sort')->default(0);
            $table->dateTimeTz('published_at')->useCurrent();
            $table->dateTimeTz('expired_at')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_container_components');
    }
};
