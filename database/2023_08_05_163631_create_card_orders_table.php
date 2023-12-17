<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('CardHolderId')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('ProductCode')->nullable();
            $table->string('SecondSurname')->nullable();
            $table->string('RecipientEmail')->nullable();
            $table->string('NameOnCard')->nullable();
            $table->string('CardDesign')->nullable();
            $table->string('AdditionalCardEmbossData')->nullable();
            $table->string('Language')->nullable();
            $table->string('ISOCountryCode')->nullable();
            $table->string('Dob')->nullable();
            $table->string('Title')->nullable();
            $table->string('Address1')->nullable();
            $table->string('Address2')->nullable();
            $table->string('Address3')->nullable();
            $table->string('PostCode')->nullable();
            $table->string('City')->nullable();
            $table->string('State')->nullable();
            $table->string('HouseNumberOrBuilding')->nullable();
            $table->string('Mobile')->nullable();
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
        Schema::dropIfExists('card_orders');
    }
}
