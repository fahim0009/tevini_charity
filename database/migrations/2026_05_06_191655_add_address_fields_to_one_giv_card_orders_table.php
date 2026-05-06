<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressFieldsToOneGivCardOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('one_giv_card_orders', function (Blueprint $table) {
            $table->string('email')->nullable()->after('pin');
            $table->string('mobile')->nullable()->after('email');
            $table->string('house_number')->nullable()->after('mobile');
            $table->string('street')->nullable()->after('house_number');
            $table->string('address2')->nullable()->after('street');
            $table->string('city')->nullable()->after('address2');
            $table->string('postcode')->nullable()->after('city');
            $table->string('country')->nullable()->after('postcode');
        });
    }

    public function down()
    {
        Schema::table('one_giv_card_orders', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'mobile',
                'house_number',
                'street',
                'address2',
                'city',
                'postcode',
                'country',
            ]);
        });
    }


}
