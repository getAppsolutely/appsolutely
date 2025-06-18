<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->integer('left');
            $table->integer('right');
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->foreignId('menu_group_id')->constrained('menu_groups')->cascadeOnDelete();
            $table->string('title');
            $table->string('remark')->nullable();
            $table->string('route')->nullable();
            $table->enum('type', ['link', 'dropdown', 'divider'])->default('link');
            $table->string('icon')->nullable();
            $table->string('permission_key')->nullable();
            $table->enum('target', ['_self', '_blank', '_parent', '_top', 'modal', 'iframe'])->default('_self');
            $table->boolean('is_external')->default(false);
            $table->dateTimeTz('published_at')->useCurrent();
            $table->dateTimeTz('expired_at')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
