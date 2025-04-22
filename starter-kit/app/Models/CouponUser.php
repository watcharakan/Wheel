<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponUser extends Model
{
    use HasFactory;

    // ชื่อตารางที่ต้องการให้ Model ผูก
    protected $table = 'coupon_user';

    protected $fillable = [
        'coupon_id',
        'line_udid',
        'used_at',
    ];

    /**
     * ความสัมพันธ์กลับไปที่ Coupon
     */
    public function coupon()
    {
        // หลาย record ใน coupon_user จะ belongsTo คูปองตัวหนึ่ง
        return $this->belongsTo(Coupon::class);
    }
}
