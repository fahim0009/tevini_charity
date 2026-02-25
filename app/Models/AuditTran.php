<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTran extends Model
{
    use HasFactory;

    protected $table = 'audit_trans';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'charity_id',
        'date',
        'amount',
        'tran_data',
        'status',
        'updated_by',
        'created_by',
    ];

    /**
     * If you want to automatically cast the JSON data back to an array 
     * when you access it later, add this:
     */
    protected $casts = [
        'tran_data' => 'array',
    ];

}
