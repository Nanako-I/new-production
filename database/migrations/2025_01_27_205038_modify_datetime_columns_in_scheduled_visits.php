<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scheduled_visits', function (Blueprint $table) {
            $table->date('arrival_datetime')->change();
            $table->date('exit_datetime')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('scheduled_visits', function (Blueprint $table) {
            $table->dateTime('arrival_datetime')->change();
            $table->dateTime('exit_datetime')->nullable()->change();
        });
    }
};
