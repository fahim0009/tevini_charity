<?php
namespace App\Models\OneGiv;
use Illuminate\Database\Eloquent\Model;

class OneGivNotification extends Model
{
    protected $fillable = [
        'reason',
        'card_serial_number',
        'payload',
    ];
}