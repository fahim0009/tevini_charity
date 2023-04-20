<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvouchersImages extends Model
{
    use HasFactory;

    protected $table = 'provouchers_images';

    protected $fillable = [
        'provouchers_id',
        'image_name',
        'created_at',
    ];

}
