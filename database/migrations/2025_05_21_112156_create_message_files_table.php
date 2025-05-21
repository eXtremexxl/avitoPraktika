<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageFilesTable extends Migration
{
    public function up()
    {
        Schema::create('message_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_files');
    }
}