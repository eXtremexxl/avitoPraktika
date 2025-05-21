<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedToAdsTable extends Migration
{
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->boolean('approved')->default(false)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
}