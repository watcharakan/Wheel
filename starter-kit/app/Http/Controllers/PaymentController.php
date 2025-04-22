<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createPromptpayIntent(Request $request)
    {
        // อ่านจำนวนเงิน (หน่วยเป็นสตางค์ ถ้า 600 บาท => 60000)
        $amount = $request->input('amount', 20000);

        // ตั้งค่า secret key
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        try {
            // สร้าง PaymentIntent สำหรับ PromptPay
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,             // จำนวนเงินในสตางค์
                'currency' => 'thb',
                'payment_method_types' => ['promptpay'],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntent' => $paymentIntent,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
