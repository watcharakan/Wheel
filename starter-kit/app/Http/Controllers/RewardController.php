<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index()
    {
        return Reward::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'color' => 'nullable|string',
            'quantity' => 'required|integer',
            'chance' => 'required|integer',
        ]);

        $reward = Reward::create($validated);

        return response()->json($reward, 201);
    }

    public function show($id)
    {
        return Reward::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $reward = Reward::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'detail' => 'nullable|string',
            'color' => 'nullable|string',
            'quantity' => 'sometimes|integer',
            'chance' => 'sometimes|integer',
        ]);

        $reward->update($validated + [
            'user_id' => $request->input('user_id', $reward->user_id),
            'project_id' => $request->input('project_id', $reward->project_id),
        ]);

        return response()->json($reward);
    }

    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->delete();

        return response()->json(null, 204);
    }
}

