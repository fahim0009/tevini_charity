<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProvoucherFieldsToProcessedBarcodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('processed_barcodes', function (Blueprint $table) {
            $table->unsignedBigInteger('provoucher_batch_id')
                ->after('barcode')
                ->nullable();

            $table->string('batch_no')
                ->after('provoucher_batch_id')
                ->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processed_barcodes', function (Blueprint $table) {
            //
        });
    }
}
