<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ImageRequest;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    //admin create video
    public function store(ImageRequest $request)
    {
        $image = Image::make($request->all());
        $savePath = \Illuminate\Support\Facades\Config::get('constants.image_folder.images.save_path');
        $file = $request->file('image_url');

        if ($file) {
            $filename = 'image' . rand(0000000, 999999) . '.' . $file->extension();
            $file->storeAs($savePath, $filename);
            $image->image_url = $filename;
        }
        $image->save();
        return response()->json(['success' => true, 'data' => $image]);
    }

    //admin update video
    public function update(ImageRequest $request, Image $image)
    {
        $oldPath = $image->image_path;
        $image->update($request->all());
        $savePath = \Illuminate\Support\Facades\Config::get('constants.image_folder.images.save_path');

        $file = $request->file('image_url');
        if ($file) {
            $filename = 'image' . rand(0000000, 999999) . '.' . $file->extension();
            if ($oldPath) {
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }
            $file->storeAs($savePath, $filename);
            $image->image_url = $filename;
            $image->update(['video_url' => $filename]);
        }
        return response()->json(['success' => true, 'data' => $image]);
    }

    //admin delete video
    public function destroy(Image $image)
    {
        $oldPath = $image->image_path;
        if ($oldPath) {
            if (Storage::exists($oldPath)) {
                Storage::delete($oldPath);
            }
        }
        return response()->json(['success' => $image->delete()]);
    }
}
