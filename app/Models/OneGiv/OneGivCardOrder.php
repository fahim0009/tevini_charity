<?php
namespace App\Models\OneGiv;
use Illuminate\Database\Eloquent\Model;

class OneGivCardOrder extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'order_number',
        'card_holder', 'fixed_amount', 'amount',
        'pin', 'status',
    ];
}