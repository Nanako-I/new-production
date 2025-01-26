<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLineColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('line_user_id')->unique()->nullable(); // LINEユーザーID
            $table->string('line_name')->nullable(); // 表示名
            $table->string('line_profile_picture')->nullable(); // プロフィール画像のURL
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('line_user_id'); // LINEユーザーIDを削除
            $table->dropColumn('line_name'); // 表示名を削除
            $table->dropColumn('line_profile_picture'); // プロフィール画像のURLを削除
        });
    }
}