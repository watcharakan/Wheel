<?php

namespace App\Http\Controllers;

use App\Models\QuizUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;  // ใช้ Facade Http
use Illuminate\Support\Facades\Log;

class QuizUserController extends Controller
{
    /**
     * GET /api/quiz-users
     */
    public function index()
    {
        $quizUsers = QuizUser::all();
        return response()->json($quizUsers);
    }

    /**
     * POST /api/quiz-users
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'nullable|string|max:255',
            'phone' => 'nullable|regex:/^[0-9]{9,15}$/',
            'email' => 'nullable|email',
        ]);

        $quizUser = QuizUser::create([
            'line_udid'    => $request->input('line_udid'),
            'line_pic'     => $request->input('line_pic'),
            'line_name'    => $request->input('line_name'),
            'name'         => $request->input('name'),
            'phone'        => $request->input('phone'),
            'email'        => $request->input('email'),
            'otp_verified' => false,
            'styles'       => [],
            'types'        => [],
            'quiz_link'    => null,
            'is_active'    => true,
            'coin'         => 0,
            'quiz_attempts'=> 0,
        ]);

        return response()->json($quizUser, 201);
    }

    /**
     * GET /api/quiz-users/{id}
     */
    public function show($id)
    {
        $quizUser = QuizUser::findOrFail($id);
        return response()->json($quizUser);
    }

    /**
     * PUT/PATCH /api/quiz-users/{id}
     */
    public function update(Request $request, $id)
    {
        $quizUser = QuizUser::findOrFail($id);

        $request->validate([
            'name'      => 'sometimes|string|max:255',
            'phone'     => 'sometimes|regex:/^[0-9]{9,15}$/',
            'email'     => 'sometimes|email',
            'is_active' => 'sometimes|boolean',
        ]);

        $quizUser->update($request->all());

        return response()->json($quizUser);
    }

    /**
     * DELETE /api/quiz-users/{id}
     */
    public function destroy($id)
    {
        $quizUser = QuizUser::findOrFail($id);
        $quizUser->delete();
        return response()->json(null, 204);
    }

    /**
     * POST /api/quiz-users/store-or-update
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'line_udid' => 'required|string',
        ]);

        $quizUser = QuizUser::updateOrCreate(
            ['line_udid' => $request->input('line_udid')],
            [
                'line_pic'  => $request->input('line_pic'),
                'line_name' => $request->input('line_name'),
                'name'      => $request->input('name'),
                'phone'     => $request->input('phone'),
                'email'     => $request->input('email'),
            ]
        );

        return response()->json($quizUser, 200);
    }

    /**
     * GET /api/users/profile?line_udid=xxxx
     */
    public function profileByLineUdid(Request $request)
    {
        $lineUdid = $request->input('line_udid');
        $quizUser = QuizUser::where('line_udid', $lineUdid)->first();

        if (!$quizUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id'        => $quizUser->id,
                'line_udid' => $quizUser->line_udid,
                'line_name' => $quizUser->line_name,
                'line_pic'  => $quizUser->line_pic,
                'name'      => $quizUser->name,
                'phone'     => $quizUser->phone,
                'email'     => $quizUser->email,
                'coin'      => $quizUser->coin ?? 0,
                'styles'    => $quizUser->styles,
                'types'     => $quizUser->types,
            ]
        ], 200);
    }

    /**
     * POST /api/quiz-users/apply-coupon
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'line_udid'   => 'required|string',
            'coupon_code' => 'required|string'
        ]);

        $quizUser = QuizUser::where('line_udid', $request->input('line_udid'))->first();
        if (!$quizUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $couponCode = $request->input('coupon_code');
        if ($couponCode !== 'Be105') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ], 400);
        }

        $addedCoin = 105;
        $quizUser->coin = ($quizUser->coin ?? 0) + $addedCoin;
        $quizUser->save();

        return response()->json([
            'success'    => true,
            'message'    => 'Coupon applied successfully',
            'added_coin' => $addedCoin,
            'total_coin' => $quizUser->coin
        ], 200);
    }

    /**
     * POST /api/quiz-users/deduct-coin
     */
    public function deductCoinForQuiz(Request $request)
    {
        $request->validate([
            'line_udid' => 'required|string',
            'quiz_cost' => 'required|integer|min:1'
        ]);

        $quizUser = QuizUser::where('line_udid', $request->input('line_udid'))->first();
        if (!$quizUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $currentCoin = $quizUser->coin ?? 0;
        $cost        = $request->input('quiz_cost');

        if ($currentCoin < $cost) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough coin'
            ], 400);
        }

        $quizUser->coin = $currentCoin - $cost;
        $quizUser->save();

        return response()->json([
            'success'        => true,
            'message'        => 'Coin deducted successfully',
            'coin_remaining' => $quizUser->coin
        ], 200);
    }

    /**
     * POST /api/quiz-users/submit-quiz
     */
    public function submitQuizResult(Request $request)
    {
        $request->validate([
            'line_udid' => 'required|string',
            'quiz_link' => 'required',
            'styles'    => 'nullable|array',
            'types'     => 'nullable|array',
        ]);

        $quizUser = QuizUser::firstOrCreate([
            'line_udid' => $request->line_udid
        ]);

        $quizLink = $request->quiz_link;
        if (is_array($quizLink)) {
            $quizLink = json_encode($quizLink);
        }

        $quizUser->quiz_link = $quizLink;
        $quizUser->styles    = $request->styles ?? [];
        $quizUser->types     = $request->types ?? [];
        $quizUser->quiz_attempts = $quizUser->quiz_attempts + 1;
        $quizUser->save();

        return response()->json([
            'success' => true,
            'message' => 'Quiz result submitted.',
            'data'    => $quizUser
        ], 200);
    }

    /**
     * POST /api/quiz-users/send-flex-to-multiple
     * ส่ง Flex Message ถึงหลายคนด้วย Facade Http
     */
    public function sendFlexToMultiple(Request $request)
    {
        $request->validate([
            'line_udids' => 'required|array',
            'flex_data'  => 'required'
        ]);

        $lineUdidList = $request->input('line_udids');
        $flexData     = $request->input('flex_data');

        // แปลง flex_data เป็น array
        $decodedFlex = json_decode($flexData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid flex_data JSON'
            ], 400);
        }

        // สร้าง "messages" สำหรับ LINE
        $messages = [
            [
                'type'     => 'flex',
                'altText'  => 'Flex Message',
                'contents' => $decodedFlex
            ]
        ];

        // ดึง Channel Access Token จาก .env
        $channelAccessToken = env('LINE_CHANNEL_ACCESS_TOKEN', '');
        if (!$channelAccessToken) {
            return response()->json([
                'success' => false,
                'message' => 'LINE_CHANNEL_ACCESS_TOKEN not found in .env'
            ], 500);
        }

        $results = [];

        foreach ($lineUdidList as $udid) {
            if (!$udid) {
                $results[] = [
                    'udid'    => $udid,
                    'success' => false,
                    'message' => 'Empty line_udid'
                ];
                continue;
            }

            // เรียก API /v2/bot/message/push
            $response = Http::withToken($channelAccessToken)
                ->withOptions(['verify' => false]) // หากเซิร์ฟเวอร์มีปัญหา SSL
                ->post('https://api.line.me/v2/bot/message/push', [
                    'to'       => $udid,
                    'messages' => $messages,
                ]);

            if ($response->successful()) {
                $results[] = [
                    'udid'    => $udid,
                    'success' => true,
                    'message' => 'Sent OK',
                    'lineApi' => $response->json()
                ];
            } else {
                $results[] = [
                    'udid'    => $udid,
                    'success' => false,
                    'message' => 'LINE API error: ' . $response->status(),
                    'lineApi' => $response->json(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Send Flex via Http to multiple users completed',
            'results' => $results,
        ]);
    }
}
