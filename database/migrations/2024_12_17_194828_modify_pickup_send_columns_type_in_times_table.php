<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('times', function (Blueprint $table) {
            // 既存のpick_upとsendカラムを削除
            $table->dropColumn('pick_up');
            $table->dropColumn('send');

            // is_absentは新規追加
            $table->boolean('is_absent')->default(false);
        });
    }

    public function down()
    {
        Schema::table('times', function (Blueprint $table) {
            // 追加したカラムを削除
            $table->dropColumn('is_absent');

            // 元のpick_upとsendカラムを復元
            $table->string('pick_up')->nullable();
        });
    }
};