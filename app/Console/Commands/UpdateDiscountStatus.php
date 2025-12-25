<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Discount;

class UpdateDiscountStatus extends Command
{
    protected $signature = 'discount:update-status';
    protected $description = '할인 종료일이 지난 할인 자동 비활성화';

    public function handle()
    {
        $today = now()->toDateString();

        $count = Discount::whereNotNull('end_at')
            ->whereDate('end_at', '<', $today)
            ->where('is_active', 1)
            ->update(['is_active' => 0]);

        $this->info("종료된 할인 {$count}건 비활성화 완료");
    }
}