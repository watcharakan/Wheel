<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'place.place_id' => 'required|string',
            'place.name' => 'required|string',
            'place.formatted_address' => 'required|string',
            'project.name' => 'required|string',
        ]);

        $userId = Auth::id();

        $store = Store::create([
            'user_id'      => $userId,
            'place_id'     => $request->input('place.place_id'),
            'name'         => $request->input('place.name'),
            'address'      => $request->input('place.formatted_address'),
            'review_link'  => $request->input('review_link'),
            'contact_name' => $request->input('registerForm.fullname'),
            'contact_tel'  => $request->input('registerForm.tel'),
        ]);

        $project = $request->input('project', []);

        $campaign = Campaign::create([
            'user_id'          => $userId,
            'store_id'         => $store->id,
            'name'             => $project['name'] ?? '',
            'description'      => $project['description'] ?? null,
            'theme_color'      => $project['themeColor'] ?? null,
            'text_color'       => $project['textColor'] ?? null,
            'banner_image_url' => $project['bannerImageUrl'] ?? null,
            'profile_image_url'=> $project['profileImageUrl'] ?? null,
        ]);

        return response()->json($campaign, 201);
    }
}
