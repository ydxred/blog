@extends('front.layout')

@section('title', '404 · 这一页走丢了')
@section('description', '你访问的页面不存在或已被移动。')

@section('styles')
@verbatim
<style>
    .c404 {
        position: relative;
        min-height: 62vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
        padding: 2rem 1rem 3rem;
    }

    /* 背景像素碎光 */
    .c404-sky { position: absolute; inset: 0; pointer-events: none; }
    .c404-sparkle {
        position: absolute;
        width: 9px; height: 9px;
        background: #e0a24f;
        image-rendering: pixelated;
        opacity: 0;
        animation: c404-twinkle 3s steps(2, end) infinite;
    }
    .c404-sparkle.s1 { top: 15%; left: 17%; background: #d97757; animation-delay: 0s; }
    .c404-sparkle.s2 { top: 24%; left: 80%; width: 12px; height: 12px; animation-delay: .5s; }
    .c404-sparkle.s3 { top: 66%; left: 13%; width: 11px; height: 11px; animation-delay: 1.1s; }
    .c404-sparkle.s4 { top: 76%; left: 79%; background: #d97757; animation-delay: 1.6s; }
    .c404-sparkle.s5 { top: 42%; left: 7%;  width: 7px;  height: 7px; animation-delay: 2.1s; }
    .c404-sparkle.s6 { top: 10%; left: 53%; width: 8px;  height: 8px; animation-delay: 2.6s; }

    /* 4 _ 4 舞台 */
    .c404-stage {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: clamp(.5rem, 3vw, 1.75rem);
        position: relative;
        z-index: 1;
    }
    .c404-num {
        font-family: 'Noto Serif SC', serif;
        font-weight: 700;
        font-size: clamp(5.5rem, 20vw, 11rem);
        line-height: 1;
        color: #cbd5e1;
        text-shadow: 0 6px 16px rgba(148, 163, 184, .25);
        user-select: none;
    }

    /* 像素小怪 */
    .c404-pix-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: clamp(.5rem, 2vw, 1.5rem);
    }
    .c404-pix {
        width: clamp(118px, 30vw, 180px);
        transform-origin: bottom center;
        animation: c404-hop 1.5s ease-in-out infinite;
    }
    .c404-pix svg {
        display: block;
        width: 100%;
        height: auto;
        image-rendering: pixelated;
        shape-rendering: crispEdges;
    }
    .c404-pix-shadow {
        margin-top: 5px;
        width: 74%;
        height: 9px;
        border-radius: 50%;
        background: radial-gradient(ellipse at center, rgba(120, 53, 32, .32), transparent 72%);
        transform-origin: center;
        animation: c404-pixshadow 1.5s ease-in-out infinite;
    }
    .c404 .eye {
        transform-box: fill-box;
        transform-origin: center;
        animation: c404-blink 3.6s infinite;
    }

    /* 文案 + 按钮 */
    .c404-title {
        margin-top: 2rem;
        font-family: 'Noto Serif SC', serif;
        font-weight: 700;
        font-size: clamp(1.4rem, 4.5vw, 2rem);
        color: #334155;
        position: relative; z-index: 1;
    }
    .c404-sub {
        margin-top: .65rem;
        color: #64748b;
        font-size: .95rem;
        line-height: 1.7;
        max-width: 28rem;
        position: relative; z-index: 1;
    }
    .c404-actions {
        margin-top: 1.8rem;
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        justify-content: center;
        position: relative; z-index: 1;
    }
    .c404-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .6rem 1.4rem;
        border-radius: 9999px;
        font-size: .9rem;
        font-weight: 600;
        transition: all .2s ease;
        text-decoration: none;
    }
    .c404-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, #e8875e, #d97757 55%, #c15f3c);
        box-shadow: 0 8px 20px rgba(193, 95, 60, .28);
    }
    .c404-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(193, 95, 60, .36); }
    .c404-btn-ghost {
        color: #475569;
        background: #fff;
        box-shadow: 0 1px 0 rgba(15, 23, 42, .04);
        border: 1px solid #e2e8f0;
    }
    .c404-btn-ghost:hover { border-color: #cbd5e1; color: #1e293b; background: #f8fafc; }

    /* 蹦跳：起跳前下蹲、腾空拉伸、落地压扁 */
    @keyframes c404-hop {
        0%   { transform: translateY(0)     scaleX(1)    scaleY(1); }
        10%  { transform: translateY(0)     scaleX(1.08) scaleY(.90); }
        45%  { transform: translateY(-24px) scaleX(.96)  scaleY(1.06); }
        72%  { transform: translateY(0)     scaleX(1)    scaleY(1); }
        82%  { transform: translateY(0)     scaleX(1.07) scaleY(.92); }
        100% { transform: translateY(0)     scaleX(1)    scaleY(1); }
    }
    @keyframes c404-pixshadow {
        0%, 72%, 100% { transform: scaleX(1);  opacity: .55; }
        45%           { transform: scaleX(.55); opacity: .25; }
    }
    @keyframes c404-blink {
        0%, 92%, 100% { transform: scaleY(1); }
        96%           { transform: scaleY(.14); }
    }
    @keyframes c404-twinkle {
        0%, 100% { opacity: 0; }
        50%      { opacity: .9; }
    }

    @media (prefers-reduced-motion: reduce) {
        .c404 *, .c404 .c404-pix { animation: none !important; }
        .c404-sparkle { opacity: .5; }
    }
</style>
@endverbatim
@endsection

@section('content')
<div class="c404">
    <div class="c404-sky" aria-hidden="true">
        <span class="c404-sparkle s1"></span>
        <span class="c404-sparkle s2"></span>
        <span class="c404-sparkle s3"></span>
        <span class="c404-sparkle s4"></span>
        <span class="c404-sparkle s5"></span>
        <span class="c404-sparkle s6"></span>
    </div>

    <div class="c404-stage">
        <span class="c404-num">4</span>

        <div class="c404-pix-wrap">
            <div class="c404-pix">
                <svg viewBox="0 0 14 13" role="img" aria-label="迷路的像素小怪">
                    {{-- 身体（水平 RLE 合并，crispEdges 无接缝） --}}
                    <rect x="4" y="0" width="6" height="1" fill="#c96c4a"/>
                    <rect x="2" y="1" width="10" height="1" fill="#c96c4a"/>
                    <rect x="1" y="2" width="2" height="1" fill="#c96c4a"/>
                    <rect x="3" y="2" width="2" height="1" fill="#de8967"/>
                    <rect x="5" y="2" width="8" height="1" fill="#c96c4a"/>
                    <rect x="0" y="3" width="14" height="1" fill="#c96c4a"/>
                    <rect x="0" y="4" width="14" height="1" fill="#c96c4a"/>
                    <rect x="0" y="5" width="4" height="1" fill="#c96c4a"/>
                    <rect x="6" y="5" width="2" height="1" fill="#c96c4a"/>
                    <rect x="10" y="5" width="4" height="1" fill="#c96c4a"/>
                    <rect x="0" y="6" width="4" height="1" fill="#c96c4a"/>
                    <rect x="6" y="6" width="2" height="1" fill="#c96c4a"/>
                    <rect x="10" y="6" width="4" height="1" fill="#c96c4a"/>
                    <rect x="0" y="7" width="14" height="1" fill="#c96c4a"/>
                    <rect x="0" y="8" width="14" height="1" fill="#c96c4a"/>
                    <rect x="1" y="9" width="12" height="1" fill="#c96c4a"/>
                    <rect x="1" y="10" width="12" height="1" fill="#a94f31"/>
                    <rect x="2" y="11" width="2" height="1" fill="#c96c4a"/>
                    <rect x="6" y="11" width="2" height="1" fill="#c96c4a"/>
                    <rect x="10" y="11" width="2" height="1" fill="#c96c4a"/>
                    <rect x="2" y="12" width="2" height="1" fill="#c96c4a"/>
                    <rect x="6" y="12" width="2" height="1" fill="#c96c4a"/>
                    <rect x="10" y="12" width="2" height="1" fill="#c96c4a"/>
                    {{-- 眼睛（会眨） --}}
                    <rect class="eye" x="4" y="5" width="2" height="2" fill="#251d1a"/>
                    <rect class="eye" x="8" y="5" width="2" height="2" fill="#251d1a"/>
                </svg>
            </div>
            <div class="c404-pix-shadow"></div>
        </div>

        <span class="c404-num">4</span>
    </div>

    <h1 class="c404-title">这一页好像走丢了</h1>
    <p class="c404-sub">你要找的内容可能被移动、删除，或者从来就不存在。<br>点下面这只小家伙带你回到有内容的地方。</p>

    <div class="c404-actions">
        <a href="{{ route('home') }}" class="c404-btn c404-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
            回首页
        </a>
        <a href="{{ route('search') }}" class="c404-btn c404-btn-ghost">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
            去搜索
        </a>
    </div>
</div>
@endsection
