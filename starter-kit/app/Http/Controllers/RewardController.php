<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    public function store(Request $request, $campaignId)
    {
        $request->validate([
            'name'     => 'required|string',
            'detail'   => 'nullable|string',
            'color'    => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'chance'   => 'required|integer|min:1',
        ]);

        $reward = Reward::create([
            'user_id'     => Auth::id(),
            'campaign_id' => $campaignId,
            'name'        => $request->name,
            'detail'      => $request->detail,
            'color'       => $request->color,
            'quantity'    => $request->quantity,
            'chance'      => $request->chance,
        ]);

        return response()->json($reward, 201);
    }
}
