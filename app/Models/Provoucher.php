<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provoucher extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'charity_id',
      'user_id',
      'batch_id',
      'donor_acc',
      'cheque_no',
      'voucher_type',
      'amount',
      'note',
      'status',

      'updated_by',
      'created_by',
  ];

    public function user(){
        return $this->belongsTo('App\Models\User');
      }

      public function charity(){
        return $this->belongsTo('App\Models\Charity')->withDefault(function ($charity, $provoucher) {
            if (!$charity) {
            return null;
            }
        });
      }
}
