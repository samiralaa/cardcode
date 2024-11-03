<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
            $table->string('title_color')->default('black');
            $table->string('background_color')->default('blue');
            $table->string('icon_color')->default('black');
            $table->string('share_color')->default('yellow');
            $table->string('qr_image')->nullable();
            $table->string('image');
            $table->timestamps();
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
