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
        Schema::create('hogosha_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')->constrained('people')->onDelete('cascade'); // 外部キー制約を追加
            $table->text('notebook');
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
        Schema::dropIfExists('hogosha_texts');
    }
};
