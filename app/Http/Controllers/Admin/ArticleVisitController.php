<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleVisit;
use Illuminate\Http\Request;

class ArticleVisitController extends Controller
{
    public function index(Request $request)
    {
        $visits = ArticleVisit::with(['article', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.visits.index', compact('visits'));
    }
}
