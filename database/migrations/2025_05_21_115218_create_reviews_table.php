<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->comment('1 to 5 stars');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['reviewer_id', 'reviewed_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};