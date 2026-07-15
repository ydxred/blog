{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $siteTitle }}</title>
        <link>{{ route('home') }}</link>
        <description>{{ $siteTagline }}</description>
        <language>zh-CN</language>
        <atom:link href="{{ url('/rss') }}" rel="self" type="application/rss+xml"/>
@foreach($articles as $article)
        <item>
            <title>{{ $article->title }}</title>
            <link>{{ route('article.show', $article->slug) }}</link>
            <guid isPermaLink="true">{{ route('article.show', $article->slug) }}</guid>
            <pubDate>{{ $article->published_at->toRssString() }}</pubDate>
            <description>{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 200) }}</description>
        </item>
@endforeach
    </channel>
</rss>
