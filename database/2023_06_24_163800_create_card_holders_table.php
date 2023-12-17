<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardHoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_holders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('CardHolderId')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('UserName')->nullable();
            $table->string('SecondSurname')->nullable();
            $table->string('Email')->nullable();
            $table->string('Password')->nullable();
            $table->string('Mobile')->nullable();
            $table->string('LandlineTelephone')->nullable();
            $table->string('DateOfBirth')->nullable();
            $table->string('SocialSecurityNumber')->nullable();
            $table->string('IdCardNumber')->nullable();
            $table->string('Nationality')->nullable();
            $table->string('Title')->nullable();
            $table->string('TaxIdCardNumber')->nullable();
            $table->string('HouseNumberOrBuilding')->nullable();
            $table->string('Address1')->nullable();
            $table->string('Address2')->nullable();
            $table->string('PostCode')->nullable();
            $table->string('State')->nullable();
            $table->string('City')->nullable();
            $table->string('Language')->nullable();
            $table->string('OnfidoId')->nullable();
            $table->string('Country')->nullable();
            $table->string('Gender')->nullable();
            $table->integer('status')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('card_holders');
    }
}
