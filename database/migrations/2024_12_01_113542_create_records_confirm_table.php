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
        if (!Schema::hasTable('records_confirm')) {
            Schema::create('records_confirm', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('person_id');
                $table->date('kiroku_date');
                $table->boolean('is_confirmed')->default(false);
                $table->timestamps();
                
                $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            });
        } else {
            Schema::table('records_confirm', function (Blueprint $table) {
                if (!Schema::hasColumn('records_confirm', 'person_id')) {
                    $table->unsignedBigInteger('person_id');
                }
                if (!Schema::hasColumn('records_confirm', 'kiroku_date')) {
                    $table->date('kiroku_date');
                }
                if (!Schema::hasColumn('records_confirm', 'is_confirmed')) {
                    $table->boolean('is_confirmed')->default(false);
                }
                
                // 外部キー制約が存在しない場合のみ追加
                if (!Schema::hasColumn('records_confirm', 'person_id')) {
                    $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records_confirm');
    }
};