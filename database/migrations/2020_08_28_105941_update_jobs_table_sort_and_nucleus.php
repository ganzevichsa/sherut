<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateJobsTableSortAndNucleus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->enum('how_to_sort',['מיונים מוקדמים','שאלון העדפות','סיירות רגילות'])->after('other_hr_phone')->nullable();
            $table->enum('nucleus',['כן','לא'])->after('how_to_sort')->nullable();
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
            $table->dropColumn('how_to_sort');
            $table->dropColumn('nucleus');
        });
    }
}
