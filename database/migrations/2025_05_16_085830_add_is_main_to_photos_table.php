<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsMainToPhotosTable extends Migration
{
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->boolean('is_main')->default(false)->after('path');
        });
    }

    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('is_main');
        });
    }
}