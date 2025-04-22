<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizUser extends Model
{
    use HasFactory;

    // กรณีต้องการระบุตารางที่ใช้
    protected $table = 'quiz_users';

    protected $fillable = [
        'line_udid',
        'line_pic',
        'line_name',
        'name',
        'phone',
        'email',
        'otp_verified',
        'styles',
        'types',
        'quiz_link',
        'is_active',
        'coin',
        'quiz_attempts',
        'pdpa_accepted',
    ];

    /**
     * กำหนด casting สำหรับฟิลด์ที่เป็น JSON หรือ boolean
     */
    protected $casts = [
        'styles'        => 'array',
        'types'         => 'array',
        'otp_verified'  => 'boolean',
        'is_active'     => 'boolean',
        'pdpa_accepted' => 'boolean',
    ];
}
