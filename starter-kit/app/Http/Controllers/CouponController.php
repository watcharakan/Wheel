<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// เพิ่ม Model ที่ต้องใช้งาน
use App\Models\Coupon;
use App\Models\QuizUser;

class CouponController extends Controller
{
    /**
     * ดึงรายการคูปองทั้งหมด
     */
    public function index()
    {
        return Coupon::all();
    }

    /**
     * สร้างคูปองใหม่
     */
    public function store(Request $request)
    {
        // validate ข้อมูลตามต้องการ
        $validated = $request->validate([
            'code'          => 'required|string',
            'max_uses'      => 'required|integer',
            'coin_reward'   => 'required|integer',
            'coupon_detail' => 'nullable|string',
            'notes'         => 'nullable|string',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
        ]);

        $coupon = Coupon::create($validated);
        return response()->json($coupon, 201);
    }

    /**
     * แสดงคูปอง 1 รายการ
     */
    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return $coupon;
    }

    /**
     * อัปเดตคูปอง
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code'          => 'required|string',
            'max_uses'      => 'required|integer',
            'coin_reward'   => 'required|integer',
            'coupon_detail' => 'nullable|string',
            'notes'         => 'nullable|string',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
        ]);

        $coupon->update($validated);
        return response()->json($coupon);
    }

    /**
     * ลบคูปอง
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(null, 204);
    }

    /**
     * APPLY COUPON (ตรวจสอบโค้ด + จำกัดการใช้ + ให้ coin)
     * POST /api/coupons/apply-coupon
     */
    public function applyCoupon(Request $request)
    {
        // รับ line_udid และ coupon_code
        $validated = $request->validate([
            'line_udid'   => 'required|string',
            'coupon_code' => 'required|string',
        ]);

        // หา coupon จาก code
        $coupon = Coupon::where('code', $validated['coupon_code'])->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon does not exist.',
            ], 404);
        }

        // ตรวจสอบช่วงวันที่อนุญาต
        $now = Carbon::now();
        if (($coupon->start_date && $now->lt($coupon->start_date)) ||
            ($coupon->end_date && $now->gt($coupon->end_date))) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not valid at this time.',
            ], 400);
        }

        // ตรวจสอบว่าใช้เกิน max_uses หรือยัง (รวมทุกคน)
        $usedCount = DB::table('coupon_user')
            ->where('coupon_id', $coupon->id)
            ->count();

        if ($usedCount >= $coupon->max_uses) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon usage limit reached.',
            ], 400);
        }

        // ตรวจสอบว่า user คนนี้เคยใช้คูปองนี้ไปแล้วหรือยัง
        $alreadyUsed = DB::table('coupon_user')
            ->where('coupon_id', $coupon->id)
            ->where('line_udid', $validated['line_udid'])
            ->exists();

        if ($alreadyUsed) {
            return response()->json([
                'success' => false,
                'message' => 'You have already used this coupon.',
            ], 400);
        }

        // บันทึกการใช้คูปองลง pivot table
        DB::table('coupon_user')->insert([
            'coupon_id' => $coupon->id,
            'line_udid' => $validated['line_udid'],
            'used_at'   => now(),
        ]);

        // หา user ในตาราง quiz_users แล้วอัปเดต coin
        $quizUser = QuizUser::where('line_udid', $validated['line_udid'])->first();
        if ($quizUser) {
            $quizUser->coin += $coupon->coin_reward;
            $quizUser->save();
        }

        // ส่งผลลัพธ์กลับ
        return response()->json([
            'success'    => true,
            'added_coin' => $coupon->coin_reward,
            'message'    => 'Coupon applied successfully.',
        ]);
    }
}
