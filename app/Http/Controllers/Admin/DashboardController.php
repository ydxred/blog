<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $days = collect(range(6, 0))->map(function($day) {
            return now()->subDays($day)->format('Y-m-d');
        });

        $visits = DB::table('article_visits')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as pv'), DB::raw('count(distinct ip_address) as uv'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartPV = [];
        $chartUV = [];

        foreach ($days as $date) {
            $dayData = $visits->get($date);
            $chartLabels[] = now()->parse($date)->format('m-d');
            $chartPV[] = $dayData ? $dayData->pv : 0;
            $chartUV[] = $dayData ? $dayData->uv : 0;
        }

        $stats = [
            'articles_count' => Article::count(),
            'posts_count' => Post::count(),
            'tags_count' => Tag::count(),
            'comments_count' => Comment::count(),
            'pending_comments' => Comment::where('status', 'pending')->count(),
            'recent_articles' => Article::with('user', 'tags')->latest()->take(5)->get(),
            'recent_posts' => Post::with('user', 'tags')->latest()->take(5)->get(),
            'chartLabels' => json_encode($chartLabels),
            'chartPV' => json_encode($chartPV),
            'chartUV' => json_encode($chartUV),
        ];

        return view('admin.dashboard', $stats);
    }
}
