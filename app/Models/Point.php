<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Point extends Model
{
    protected $fillable = ['customer_id', 'amount', 'type'];
    /// 현재 잔액 계산
    public static function balance($customerId)
    {
        return self::where('customer_id', $customerId)
            ->whereIn('type', ['earn', 'use', 'expire']) 
            ->sum(DB::raw("
                CASE 
                    WHEN type='earn' THEN amount
                    WHEN type='use' THEN amount
                    WHEN type='expire' THEN amount
                END
            "));
    }

    // 만료 확인 (6개월 지난 포인트를 expire로 기록)
    public static function expireCheck()
    {
        $expired = self::where('type', 'earn')
            ->where('created_at', '<', now()->subMonths(6))
            ->get();

        foreach ($expired as $point) {
            self::create([
                'customer_id' => $point->customer_id,
                'amount' => $point->amount,
                'type' => 'expire',
            ]);
        }
    }
}
