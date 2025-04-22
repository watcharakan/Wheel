<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    // กำหนดฟิลด์ที่อนุญาตให้ fill ได้
    protected $fillable = [
        'code',
        'max_uses',
        'coin_reward',    // เพิ่ม coin_reward ด้วย
        'coupon_detail',
        'notes',
        'start_date',
        'end_date',
    ];
}
