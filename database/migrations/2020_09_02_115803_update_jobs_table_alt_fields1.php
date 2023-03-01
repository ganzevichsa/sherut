<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateJobsTableAltFields1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->tinyInteger('active')->after('doc_urls')->default(0);
            $table->tinyInteger('checked')->after('active')->default(0);
            $table->string('year')->after('checked')->nullable();
            $table->enum('type_of_year',['שנה נוכחית','שנה הבאה','ארכיון'])->after('year')->nullable();
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
            $table->dropColumn('active');
            $table->dropColumn('checked');
            $table->dropColumn('year');
            $table->dropColumn('type_of_year');
        });
    }
}
