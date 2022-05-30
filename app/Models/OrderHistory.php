<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'order_id',
      'voucher_id',
      'number_voucher',
      'amount',
      'o_unq',
      'status',
      'updated_by',
      'created_by',
  ];

    public function order(){
        return $this->belongsTo('App\Models\Order');
      }

      public function voucher(){
        return $this->belongsTo('App\Models\Voucher');
      }
}
