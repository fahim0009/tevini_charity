<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAmountColumnInStandingDonationTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change column for standing_donations table
        DB::statement("ALTER TABLE standing_donations MODIFY COLUMN amount DECIMAL(10,2) NULL DEFAULT 0;");

        // Change column for standingdonation_details table
        DB::statement("ALTER TABLE standingdonation_details MODIFY COLUMN amount DECIMAL(10,2) NULL DEFAULT 0;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original integer type
        DB::statement("ALTER TABLE standing_donations MODIFY COLUMN amount INT(11) NULL;");
        DB::statement("ALTER TABLE standingdonation_details MODIFY COLUMN amount INT(11) NULL;");
    }
}
