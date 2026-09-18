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
      'waiting',
      'expired',
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

      public function getDonorNameAttribute()
        {
            return $this->user?->name;
        }

        public function getAccountNoAttribute()
        {
            return $this->user?->accountno;
        }
      
      public function transaction()
      {
          return $this->belongsTo(Usertransaction::class, 'tran_id', 'id');
      }


    public function scopePendingVouchers($query, $userId = null, $fromDate = null, $toDate = null)
    {
        return $query->with(['charity', 'user'])
            ->select('id','user_id','charity_id','created_at','amount','note','cheque_no','status','expired')
            ->where('waiting', 'No')
            ->where('status', '0')
            ->where(function ($q) {
                $q->where('expired', '!=', 'Yes')->orWhereNull('expired');
            })
            ->when($userId, function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when($fromDate && $toDate, function($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderBy('id', 'DESC');
    }





}
