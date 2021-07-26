<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableProviderInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_identificator')->after('digit_number')->nullable();
            $table->unsignedBigInteger('provider_id')->after('provider_identificator')->nullable();
            $table->foreign('provider_id')->references('id')->on('auth_providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_provider_identificator_foreign');
            $table->dropColumn('provider_id');
        });
    }
}
