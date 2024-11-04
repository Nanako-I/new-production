<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeJukyuushaNumberToBigintInPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            // jukyuusha_numberカラムを正数の大きな数も保存できるbigInteger型に変更
            $table->unsignedBigInteger('jukyuusha_number')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            // jukyuusha_numberカラムをinteger型に戻す
            $table->unsignedBigInteger('jukyuusha_number')->change();
        });
    }
}