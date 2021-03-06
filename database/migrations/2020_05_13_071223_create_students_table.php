<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('code');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('registered_year');
            $table->unsignedBigInteger('department_id');
            $table->enum('grade', ['Freshman', 'Sophomore', 'Junior', 'Senior', 'Graduate'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->unique('uuid');
            $table->unique(['code', 'deleted_at']);
            $table->unique(['user_id', 'deleted_at']);
            $table->index(['registered_year', 'department_id', 'grade']);
            $table->index(['department_id', 'grade']);
            $table->index('grade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
