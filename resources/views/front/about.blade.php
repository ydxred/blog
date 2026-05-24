@extends('front.layout')

@section('title', '关于我')
@section('description', '关于这个博客和它的作者。')

@section('hero')
    <x-front.hero
        title="关于我"
        subtitle="你好，欢迎来到我的角落。"
        theme="amber" />
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <article class="rounded-2xl bg-white/90 backdrop-blur-sm p-6 sm:p-10 shadow-sm ring-1 ring-slate-200/80 mb-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-400 to-pink-500"></div>
        <div class="prose prose-slate prose-lg max-w-none
                    prose-headings:font-title prose-headings:font-bold prose-headings:text-slate-800
                    prose-a:text-pink-600 hover:prose-a:text-pink-500
                    prose-code:text-pink-700 prose-code:bg-slate-100/80 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:font-medium
                    prose-pre:bg-slate-900 prose-pre:text-slate-50 prose-pre:shadow-sm
                    prose-img:rounded-xl prose-img:ring-1 prose-img:ring-slate-200/60
                    prose-blockquote:border-l-pink-400 prose-blockquote:bg-pink-50/50 prose-blockquote:py-1 prose-blockquote:pr-4 prose-blockquote:rounded-r-lg prose-blockquote:text-slate-600 prose-blockquote:not-italic mt-4">
            {!! \App\Services\MarkdownRenderer::toHtml($content, false) !!}
        </div>
    </article>
</div>
@endsection
