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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename');
            $table->string('filename');
            $table->string('extension', 50);
            $table->string('mime_type');
            $table->string('path');
            $table->bigInteger('size');
            $table->string('hash');
            $table->timestamps();
            $table->softDeletes();

            // Add indexes for commonly queried fields
            $table->index('filename');
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
