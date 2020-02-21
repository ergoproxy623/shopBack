<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    // list all images
    public function index()
    {
        return response()->json(['success' => true, 'data' => Image::all()]);
    }
}
