<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('store_id')->constrained('stores');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('theme_color')->nullable();
            $table->string('text_color')->nullable();
            $table->string('banner_image_url')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};

