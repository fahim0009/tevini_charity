<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvoucherBatch extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
    ];

    public function transaction()
    {
        return $this->hasMany('App\Models\Usertransaction');
    }


    public function provoucher(){
        return $this->hasMany('App\Models\Provoucher');
    }

    protected $appends = ['total_vouchers'];

    public function getTotalVouchersAttribute()
    {
        return $this->provoucher()->count();
    }

    public function charity()
    {
        return $this->belongsTo('App\Models\Charity');
    }

}
