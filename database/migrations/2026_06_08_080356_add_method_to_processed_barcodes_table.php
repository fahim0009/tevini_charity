<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMethodToProcessedBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('processed_barcodes', function (Blueprint $table) {
            $table->string('method')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('processed_barcodes', function (Blueprint $table) {
            $table->dropColumn('method');
        });
    }


}
