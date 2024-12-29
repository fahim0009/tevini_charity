<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function orderhistory()
    {
      return $this->hasOne('App\Models\OrderHistory');
    }

    public function orderhistories()
    {
      return $this->hasMany('App\Models\OrderHistory');
    }
}
