<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batchprov extends Model
{
    use HasFactory;


    protected $fillable = [
        'charity_id',  
        'status',  
        'updated_by',
        'created_by',
    ];
}
