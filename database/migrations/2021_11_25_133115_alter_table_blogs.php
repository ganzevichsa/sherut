<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableBlogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('blogs', function (Blueprint $table) {
          $table->string('author')->nullable()->default(null);
          $table->string('subtitle')->nullable()->default(null);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('blogs', function (Blueprint $table) {
        $table->dropColumn('author');
        $table->dropColumn('subtitle');
      });
    }
}
