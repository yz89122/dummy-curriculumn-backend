<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateI18nsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i18ns', function (Blueprint $table) {
            $table->id();
            // $table->morphs('resource');
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id');
            $table->string('locale');
            $table->text('text');
            $table->timestamps();

            $table->unique(['resource_type', 'resource_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('i18ns');
    }
}
