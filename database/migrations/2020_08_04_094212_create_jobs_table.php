<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('out_identificator')->nullable();
            $table->string('site')->nullable();
            $table->string('url')->nullable();
            $table->string('title');
            $table->integer('home')->default(0)->nullable();
            $table->integer('out')->default(0)->nullable();
            $table->integer('dormitory')->default(0)->nullable();
            $table->text('about')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->unsignedBigInteger('stage_of_education_id')->nullable();
            $table->string('other_hr_name')->nullable();
            $table->string('other_hr_phone')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('subcategory_id')->references('id')->on('subcategories');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('stage_of_education_id')->references('id')->on('stage_of_education');
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
        Schema::dropIfExists('jobs');
    }
}
