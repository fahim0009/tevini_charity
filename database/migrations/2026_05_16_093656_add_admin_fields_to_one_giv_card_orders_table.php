<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminFieldsToOneGivCardOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('one_giv_card_orders', function (Blueprint $table) {
            $table->boolean('ordered_by_admin')->default(0)->after('status');
            $table->unsignedBigInteger('admin_id')->nullable()->after('ordered_by_admin');
        });
    }

    public function down()
    {
        Schema::table('one_giv_card_orders', function (Blueprint $table) {
            $table->dropColumn(['ordered_by_admin', 'admin_id']);
        });
    }
}
