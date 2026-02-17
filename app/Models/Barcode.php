<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderhistory_id',
        'user_id',
        'barcode',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function orderhistory()
    {
        return $this->belongsTo('App\Models\OrderHistory');
    }
}
