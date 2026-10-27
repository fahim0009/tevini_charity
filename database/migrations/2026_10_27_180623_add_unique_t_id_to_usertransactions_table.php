<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueTIdToUsertransactionsTable extends Migration
{
    public function up()
    {
        // Step 1: Set NULL or empty t_id to a unique value based on id
        DB::statement("UPDATE usertransactions 
                       SET t_id = CONCAT('LEGACY-', id) 
                       WHERE t_id IS NULL OR t_id = ''");

        // Step 2: For duplicate t_id records, append the primary key id
        // to all records EXCEPT the oldest one (keeps original t_id)
        DB::statement("UPDATE usertransactions n1
                       INNER JOIN (
                           SELECT t_id, MIN(id) as min_id
                           FROM usertransactions
                           WHERE t_id IS NOT NULL AND t_id != ''
                           GROUP BY t_id
                           HAVING COUNT(*) > 1
                       ) n2 ON n1.t_id = n2.t_id AND n1.id > n2.min_id
                       SET n1.t_id = CONCAT(n1.t_id, '-', n1.id)");

        // Step 3: Verify no duplicates remain (defensive check)
        $duplicates = DB::select("
            SELECT t_id, COUNT(*) as cnt 
            FROM usertransactions 
            WHERE t_id IS NOT NULL 
            GROUP BY t_id 
            HAVING COUNT(*) > 1
            LIMIT 1
        ");
        
        if (!empty($duplicates)) {
            throw new \Exception('Cannot add unique constraint: duplicates still exist after cleanup');
        }

        // Step 4: Add unique constraint
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->unique('t_id', 'usertransactions_t_id_unique');
        });
    }

    public function down()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->dropUnique('usertransactions_t_id_unique');
        });
    }
}