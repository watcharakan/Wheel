<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'theme_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'banner_image_url' => 'nullable|string',
            'profile_image_url' => 'nullable|string',
        ]);

        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    public function show($id)
    {
        return Project::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'theme_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'banner_image_url' => 'nullable|string',
            'profile_image_url' => 'nullable|string',
        ]);

        $project->update($validated + [
            'user_id' => $request->input('user_id', $project->user_id),
            'store_id' => $request->input('store_id', $project->store_id),
        ]);

        return response()->json($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(null, 204);
    }
}

