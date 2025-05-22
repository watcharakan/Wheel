<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizUserController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\RewardController;

// Resource หลัก
Route::middleware('role:store')->group(function () {
    Route::resource('quiz-users', QuizUserController::class)->only([
        'index',
        'store',
        'show',
        'update',
        'destroy'
    ]);

    // บันทึก/อัปเดตข้อมูล PDPA หรือข้อมูลอื่น ๆ
    Route::post('quiz-users/store-or-update', [QuizUserController::class, 'storeOrUpdate']);

    // *** ดึงข้อมูล user จาก line_udid ***
    Route::get('users/profile', [QuizUserController::class, 'profileByLineUdid']);

    // *** ตรวจสอบคูปอง (ตัวอย่าง) ***
    Route::post('quiz-users/apply-coupon', [QuizUserController::class, 'applyCoupon']);

    // *** (ใหม่) ตัด coin สำหรับทำ Quiz ***
    Route::post('quiz-users/deduct-coin', [QuizUserController::class, 'deductCoinForQuiz']);

    // *** ส่งผลลัพธ์ Quiz (เก็บ styles, types, quiz_link และ +1 quiz_attempts) ***
    Route::post('quiz-users/submit-quiz', [QuizUserController::class, 'submitQuizResult']);

    // สร้างแคมเปญและรางวัล
    Route::post('campaigns', [CampaignController::class, 'store']);
    Route::post('campaigns/{campaign}/rewards', [RewardController::class, 'store']);
});

// Resource อื่น ๆ สำหรับผู้ดูแลระบบ
Route::middleware('role:admin')->group(function () {
    Route::apiResource('coupons', CouponController::class);
    Route::apiResource('packages', PackageController::class);
});

// ตัวอย่าง Payment
Route::post('/create-promptpay-intent', [PaymentController::class, 'createPromptpayIntent']);

// สำคัญ: เส้นทาง apply-coupon
Route::post('/coupons/apply-coupon', [CouponController::class, 'applyCoupon']);

Route::post('/quiz-users/send-flex-to-multiple', [QuizUserController::class, 'sendFlexToMultiple']);

// Authentication
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

