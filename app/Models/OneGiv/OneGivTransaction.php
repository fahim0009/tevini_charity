<?php
namespace App\Models\OneGiv;
use Illuminate\Database\Eloquent\Model;

class OneGivTransaction extends Model
{
    protected $fillable = [
        'terminal_id', 'onegiv_transaction_id',
        'card_issuer_transaction_id', 'card_serial_number',
        'amount', 'reference', 'charity_number',
        'account_number', 'sortcode', 'status',
    ];
}