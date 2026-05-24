<?php

namespace App\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownRenderer
{
    public static function toHtml(string $markdown, bool $stripFirstH1 = true): string
    {
        if ($stripFirstH1) {
            $markdown = self::stripLeadingH1($markdown);
        }

        $markdown = self::normalizeBlocks($markdown);

        $config = [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break' => "\n",
            ],
        ];

        $env = new Environment($config);
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new GithubFlavoredMarkdownExtension());
        $env->addExtension(new AutolinkExtension());

        $converter = new MarkdownConverter($env);

        return (string) $converter->convert($markdown);
    }

    /**
     * 去掉文档开头的 H1（因为页面已有大标题）
     */
    protected static function stripLeadingH1(string $markdown): string
    {
        return preg_replace('/^\s*#\s+[^\n]+\n+/u', '', $markdown, 1);
    }

    /**
     * 自动给标题/列表/代码块/分隔线/引用块/表格补充必要的空行
     * 不破坏内部代码块的内容
     */
    protected static function normalizeBlocks(string $markdown): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $markdown);
        $out = [];
        $inFence = false;
        $fence = '';

        foreach ($lines as $i => $line) {
            if (preg_match('/^(\s*)(```+|~~~+)(.*)$/u', $line, $m)) {
                if (!$inFence) {
                    $inFence = true;
                    $fence = $m[2];
                    if (!empty($out) && trim(end($out)) !== '') {
                        $out[] = '';
                    }
                    $out[] = $line;
                    continue;
                } elseif (strpos($m[2], $fence) === 0 || strpos($fence, $m[2]) === 0) {
                    $inFence = false;
                    $out[] = $line;
                    if (isset($lines[$i + 1]) && trim($lines[$i + 1]) !== '') {
                        $out[] = '';
                    }
                    continue;
                }
            }

            if ($inFence) {
                $out[] = $line;
                continue;
            }

            $isHeading = (bool) preg_match('/^\s{0,3}#{1,6}\s+/u', $line);
            $isHr = (bool) preg_match('/^\s{0,3}(-{3,}|\*{3,}|_{3,})\s*$/u', $line);

            if ($isHeading) {
                if (!empty($out) && trim(end($out)) !== '') {
                    $out[] = '';
                }
                $out[] = $line;
                if (isset($lines[$i + 1]) && trim($lines[$i + 1]) !== '') {
                    $out[] = '';
                }
                continue;
            }

            if ($isHr) {
                if (!empty($out) && trim(end($out)) !== '') {
                    $out[] = '';
                }
                $out[] = $line;
                if (isset($lines[$i + 1]) && trim($lines[$i + 1]) !== '') {
                    $out[] = '';
                }
                continue;
            }

            $out[] = $line;
        }

        return implode("\n", $out);
    }
}
