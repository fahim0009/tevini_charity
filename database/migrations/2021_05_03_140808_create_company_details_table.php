<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_details', function (Blueprint $table) {
            $table->id();
            $table->string('company_name',191)->nullable();
            $table->string('company_logo',191)->nullable();
            $table->string('fav_icon',191)->nullable();
            $table->string('address',191)->nullable();
            $table->string('phone1',191)->nullable();
            $table->string('phone2',191)->nullable();
            $table->string('email1',191)->nullable();
            $table->string('email2',191)->nullable();
            $table->string('website',191)->nullable();
            $table->string('footer_link',191)->nullable();
            $table->string('google_play_link',191)->nullable();
            $table->string('google_appstore_link',191)->nullable();
            $table->string('tawkto',191)->nullable();
            $table->string('facebook',191)->nullable();
            $table->string('twiter',191)->nullable();
            $table->string('instagram',191)->nullable();
            $table->string('linkedin',191)->nullable();
            $table->string('created_by',191)->nullable();
            $table->string('updated_by',191)->nullable();
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
        Schema::dropIfExists('company_details');
    }
}
