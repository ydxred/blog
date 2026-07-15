<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>会话已过期</title>
<link rel="icon" href="/favicon.ico">
<style>
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
       background:#f7f5f0;color:#3a3632;font-family:system-ui,-apple-system,"Segoe UI","Noto Sans SC",sans-serif;padding:1.5rem}
  .box{max-width:26rem;text-align:center}
  .glyph{font-size:3.2rem;line-height:1;margin-bottom:1rem}
  h1{font-size:1.35rem;margin:0 0 .6rem;font-weight:700}
  p{color:#7a736b;line-height:1.7;margin:0 0 1.6rem;font-size:.95rem}
  .btns{display:flex;gap:.6rem;justify-content:center;flex-wrap:wrap}
  a,button{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.3rem;border-radius:999px;
     font-size:.9rem;font-weight:600;text-decoration:none;cursor:pointer;border:none;font-family:inherit}
  .p{background:linear-gradient(135deg,#e0855c,#cf6f4d);color:#fff}
  .g{background:#fff;color:#5a534c;border:1px solid #e2ddd5}
  @media (prefers-color-scheme:dark){body{background:#14120f;color:#e8e4de}.g{background:#1e1b17;border-color:#2e2a24;color:#c8c2ba}p{color:#9a938a}}
</style>
</head>
<body>
  <div class="box">
    <div class="glyph">⏳</div>
    <h1>会话已过期</h1>
    <p>页面停留太久，安全令牌已失效。刷新一下就好，你填的内容可能需要重新提交。</p>
    <div class="btns">
      <button class="p" onclick="location.reload()">刷新页面</button>
      <a class="g" href="/">回首页</a>
    </div>
  </div>
</body>
</html>
