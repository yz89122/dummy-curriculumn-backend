<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('code');
            $table->unsignedBigInteger('college_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('college_id')->references('id')->on('colleges')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique('uuid');
            $table->unique(['code', 'deleted_at']);
            $table->index('college_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
