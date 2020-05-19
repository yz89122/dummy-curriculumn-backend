<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->unsignedBigInteger('course_template_id')->nullable();
            $table->integer('academic_year');
            $table->enum('academic_term', ['Fall', 'Spring', 'Summer Fall', 'Summer Spring']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('course_template_id')->references('id')->on('course_templates')
                ->onUpdate('cascade')->onDelete('set null');

            $table->index('course_template_id');
            $table->index(['academic_year', 'academic_term', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
