<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTypeOfYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_type_of_years', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('type_of_year_id')->nullable();
            $table->foreign('job_id')->references('id')->on('jobs');
            $table->foreign('type_of_year_id')->references('id')->on('type_of_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_type_of_years');
    }
}
