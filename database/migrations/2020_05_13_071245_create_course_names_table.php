<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_template_id');
            $table->string('locale');
            $table->text('text');

            $table->foreign('course_template_id')->references('id')->on('course_templates')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index(['course_template_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_names');
    }
}
