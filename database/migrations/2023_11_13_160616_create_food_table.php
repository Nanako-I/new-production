<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('people_id');
            // onDelete('cascade')は、外部キーの参照先のpeopleテーブルのidのレコードが削除された場合に、このレコードも一緒に削除されるようにする
            $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade');
            $table->string('lunch')->nullable();
            $table->string('lunch_bikou')->nullable();
            $table->string('oyatsu')->nullable();
            $table->string('oyatsu_bikou')->nullable();
            $table->string('staple_food')->nullable();
            $table->string('side_dish')->nullable();
            $table->string('medicine')->nullable();
            $table->string('medicine_name')->nullable();
            $table->string('bikou')->nullable();
            $table->dateTime('created_at')->useCurrent()->nullable(false)->change();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('food');
    }
};