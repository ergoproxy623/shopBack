<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VideoRequest;
use App\Models\User;
use App\Models\UserVideo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use JWTAuth;

class VideoController extends Controller
{
    //admin create video
    public function store(VideoRequest $request)
    {
        $video = Video::make($request->all());
        $savePath = \Illuminate\Support\Facades\Config::get('constants.video_folder.videos.save_path');
        $file = $request->file('video_url');

        if ($file) {
            $filename = 'video' . rand(0000000, 999999) . '.' . $file->extension();
            $file->storeAs($savePath, $filename);
            $video->video_url = $filename;
        }
        $video->save();
        return response()->json(['success' => true, 'data' => $video]);
    }

    //admin update video
    public function update(VideoRequest $request, Video $video)
    {
        $oldPath = $video->video_path;
        $video->update($request->all());
        $savePath = \Illuminate\Support\Facades\Config::get('constants.video_folder.videos.save_path');

        $file = $request->file('video_url');
        if ($file) {
            $filename = 'video' . rand(0000000, 999999) . '.' . $file->extension();
            if ($oldPath) {
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }
            $file->storeAs($savePath, $filename);
            $video->video_url = $filename;
            $video->update(['video_url' => $filename]);
        }
        return response()->json(['success' => true, 'data' => $video]);
    }

    //admin delete video
    public function destroy(Video $video)
    {
        $oldPath = $video->video_path;
        if ($oldPath) {
            if (Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }
        return response()->json(['success' => $video->delete()]);
    }

}

