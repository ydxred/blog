/**
 * social-share.js 仅引入样式；脚本本身是 IIFE 自执行的（依赖全局 window/document），
 * 经 Vite 打包后会被优化掉外层 IIFE 参数导致初始化失败。
 * 所以脚本走 public/vendor/ 直接 <script src>，样式走 vite bundle。
 */
import 'social-share.js/dist/css/share.min.css';
