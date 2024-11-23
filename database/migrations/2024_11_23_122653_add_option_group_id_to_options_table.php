<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->uuid('option_group_id')->after('title')->nullable();
        });
    }

    public function down()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('option_group_id');
        });
    }
};
