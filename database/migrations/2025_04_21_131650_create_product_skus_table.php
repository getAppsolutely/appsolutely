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
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('cover')->nullable();
            $table->string('keywords')->nullable();
            $table->string('description')->nullable();
            $table->text('content')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedBigInteger('original_price')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedTinyInteger('sort')->nullable();
            $table->unsignedTinyInteger('status')->default(0);

            $table->dateTimeTz('published_at')->useCurrent();
            $table->dateTimeTz('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_skus');
    }
};
