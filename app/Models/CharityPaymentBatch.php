<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharityPaymentBatch extends Model
{
    use HasFactory;

    protected $table = 'charity_payment_batches';

    protected $fillable = [
        'charity_id',
        'transaction_id',
        'last_payment_date',
        'usertransactions_ids',
        'status',
        'date',
        'amount'
    ];

    // This will automatically convert the JSON from database to PHP array and vice-versa
    protected $casts = [
        'usertransactions_ids' => 'array',
    ];
}