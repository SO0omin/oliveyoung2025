<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Point;
use Carbon\Carbon;

class ExpirePoints extends Command
{
    protected $signature = 'points:expire';
    protected $description = '유효기간(6개월)이 지난 포인트의 상태를 변경해줍니다.';

    public function handle()
    {
        $expireDate = Carbon::now()->subMonths(6);

        $count = Point::where('type', 'earn')
                    ->where('created_at', '<=', $expireDate)
                    ->update(['type' => 'expire']); // 루프 없이 한 번에 업데이트

        $this->info("Expired points: " . $count);
    }
}