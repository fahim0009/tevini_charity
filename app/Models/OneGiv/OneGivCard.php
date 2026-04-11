<?php
namespace App\Models\OneGiv;
use Illuminate\Database\Eloquent\Model;

class OneGivCard extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'order_number',
        'serial_number', 'pin', 'display_number',
        'expiry_date', 'status',
    ];
}