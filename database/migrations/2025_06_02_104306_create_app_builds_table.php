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
        Schema::create('app_builds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('version_id');
            $table->string('platform')->default('windows'); // darwin / windows / linux
            $table->string('arch')->nullable();
            $table->tinyInteger('force_update')->default(0);
            $table->json('gray_strategy')->nullable(); // e.g. {"percent": 20, "uuid_hash_range": [0, 2000]}
            $table->text('release_notes')->nullable();
            $table->string('build_status')->nullable();    // pending, success, failed
            $table->string('build_log')->nullable();
            $table->integer('assessable_id')->nullable();
            $table->string('signature')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('version_id')->references('id')->on('app_versions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_builds');
    }
};
