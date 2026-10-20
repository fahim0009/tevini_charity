<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDevNoteToProvouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provouchers', function (Blueprint $table) {
            $table->longText('dev_note')->nullable()->after('checked_date');
        });
    }

    public function down()
    {
        Schema::table('provouchers', function (Blueprint $table) {
            $table->dropColumn('dev_note');
        });
    }
}
