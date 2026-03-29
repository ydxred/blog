<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,gif,webp,heic|max:20480',
        ]);

        $path = $request->file('image')->store('articles', 'public');

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
        ]);
    }
}
