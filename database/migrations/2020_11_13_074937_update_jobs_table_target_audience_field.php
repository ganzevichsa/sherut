<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateJobsTableTargetAudienceField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('target_audience')->nullable()->after('status');
            $table->text('main_areas_of_study')->nullable()->after('target_audience');
            $table->string('route_midrasha')->nullable()->after('main_areas_of_study');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('target_audience');
            $table->dropColumn('main_areas_of_study');
            $table->dropColumn('route_midrasha');
        });
    }
}
