<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableJobReviewsUsersInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_reviews', function (Blueprint $table) {
            $table->string('phone')->default(0)->after('status');
            $table->string('first_name')->nullable()->after('phone');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('avatar')->nullable()->after('last_name');
            $table->tinyInteger('show_info')->default(0)->after('last_name');
            $table->integer('date')->nullable()->after('show_info');
            $table->integer('duration')->nullable()->after('date');
            $table->longText('description')->nullable()->after('show_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_reviews', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('first_name');
            $table->dropColumn('avatar');
            $table->dropColumn('last_name');
            $table->dropColumn('show_info');
            $table->dropColumn('description');
            $table->dropColumn('date');
            $table->dropColumn('duration');
        });
    }
}
