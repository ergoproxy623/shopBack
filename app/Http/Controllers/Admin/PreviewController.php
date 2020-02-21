<?php

namespace App\Http\Controllers\Admin;

use App\Models\Preview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use PulkitJalan\Google\Facades\Google;

class PreviewController extends Controller
{
    //admin create preview
    public function store(Request $request)
    {
        $preview = Preview::create($request->all());
        $service = Google::make('YouTube');
        $queryParams = [
            'maxResults' => 50,
            'playlistId' => $preview->preview_url,
        ];

        $response = $service->playlistItems->listPlaylistItems('snippet', $queryParams);
        return response()->json(['success' => true, 'data' => $preview, 'youtube' => $response->items]);
    }

    //admin update preview
    public function update(Request $request)
    {
        $preview =Preview::first();
        $preview->update($request->all());
        $service = Google::make('YouTube');
        $queryParams = [
            'maxResults' => 50,
            'playlistId' => $preview->preview_url,
        ];
        $response = $service->playlistItems->listPlaylistItems('snippet', $queryParams);
        return response()->json(['success' => true, 'data' => $preview, 'youtube' => $response->items]);
    }
}
