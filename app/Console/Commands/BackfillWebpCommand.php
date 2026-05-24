<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Command;

class BackfillWebpCommand extends Command
{
    protected $signature = 'images:webp {dir=articles,posts,avatars,uploads,articles/inline : 逗号分隔的子目录}';

    protected $description = '为指定 storage 子目录下的存量 jpg/png 图批量生成 webp 副本';

    public function handle(ImageOptimizer $opt): int
    {
        $dirs = explode(',', (string) $this->argument('dir'));
        $totals = ['done' => 0, 'skip' => 0, 'total' => 0];
        foreach ($dirs as $dir) {
            $dir = trim($dir);
            if (! $dir) {
                continue;
            }
            $this->info("处理 storage/{$dir} ...");
            $r = $opt->backfillDirectory($dir);
            $this->line("  生成: {$r['done']}  跳过: {$r['skip']}  总扫描: {$r['total']}");
            foreach ($r as $k => $v) {
                $totals[$k] += $v;
            }
        }
        $this->newLine();
        $this->info("完成 ✅  共生成 {$totals['done']} 个 webp，跳过 {$totals['skip']}，扫描 {$totals['total']} 个文件。");
        return self::SUCCESS;
    }
}
