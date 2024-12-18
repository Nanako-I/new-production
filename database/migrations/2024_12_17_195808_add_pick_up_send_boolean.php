<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('times', function (Blueprint $table) {
            //pick_upとsendカラムを追加
            $table->boolean('pick_up')->default(false);
            $table->boolean('send')->default(false);
        });
    }

    public function down()
    {
        Schema::table('times', function (Blueprint $table) {
            // 追加したカラムを削除
            $table->dropColumn([
                'pick_up',
                'send'
            ]);
        });
    }
};