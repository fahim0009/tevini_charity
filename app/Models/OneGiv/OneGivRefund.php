<?php

namespace App\Models\OneGiv;

use Illuminate\Database\Eloquent\Model;

class OneGivRefund extends Model
{
    protected $fillable = [
        'card_issuer_transaction_id',
        'card_serial_number',
        'user_id',
        'charity_id',
        'charity_number',
        'amount',
        'amount_pounds',
        'onegiv_transaction_id',
        'status',
        'admin_watch',
        'admin_status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function charity()
    {
        return $this->belongsTo(\App\Models\Charity::class);
    }

    public function onegivTransaction()
    {
        return $this->belongsTo(OneGivTransaction::class, 'card_issuer_transaction_id', 'card_issuer_transaction_id');
    }
}