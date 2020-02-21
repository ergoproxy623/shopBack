<?php

namespace App\Http\Controllers;

use App\Models\Preview;
use Illuminate\Http\Request;
use PulkitJalan\Google\Facades\Google;

class PreviewController extends Controller
{
    //View preview via Facebook
    public function show()
    {
        $preview = Preview::first();
        $service = Google::make('YouTube');
        $queryParams = [
            'maxResults' => 50,
            'playlistId' => $preview->preview_url,
        ];
        $response = $service->playlistItems->listPlaylistItems('snippet', $queryParams);
        return response()->json(['success' => true, 'data' => $preview, 'youtube' => $response->items]);
    }
}
