<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,gif,webp,heic|max:20480',
        ]);

        $path = $request->file('image')->store('posts', 'public');

        return response()->json([
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }
}
