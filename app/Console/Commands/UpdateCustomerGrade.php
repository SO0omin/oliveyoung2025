<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Order;

class UpdateCustomerGrade extends Command
{
    protected $signature = 'customers:update-grade';
    protected $description = '매월 1일 사용자 결제 금액을 확인하여 등급을 갱신합니다.';

    public function handle()
    {
        $customers = Customer::all();
        $oneMonthAgo = now()->subMonth();     
        $sixMonthsAgo = now()->subMonths(6);  

        foreach($customers as $customer) {
            // 1. 최근 1개월간 결제 금액
            $monthlyAmount = Order::where('customer_id', $customer->id)
                ->where('created_at', '>=', $oneMonthAgo)
                ->where('status', 'paid')
                ->sum('total_price');

            // 2. 최근 6개월간 구매 횟수
            $sixMonthOrdersCount = Order::where('customer_id', $customer->id)
                ->where('created_at', '>=', $sixMonthsAgo)
                ->where('status', 'paid')
                ->count();

            // 3. 전체 기간 구매 횟수
            $totalOrdersCount = Order::where('customer_id', $customer->id)
                ->where('status', 'paid')
                ->count();

            // --- 등급 결정 로직 ---
            if ($totalOrdersCount == 0) {
                $newGrade = 'newbie'; 
            } elseif ($sixMonthOrdersCount == 0) {
                $newGrade = 'returning'; 
            } else {
                if ($monthlyAmount >= 200000) {
                    $newGrade = 'vip';
                } elseif ($monthlyAmount >= 100000) {
                    $newGrade = 'gold';
                } elseif ($monthlyAmount >= 50000) {
                    $newGrade = 'silver';
                } else {
                    $newGrade = 'bronze'; 
                }
            }

            $customer->update(['grade' => $newGrade]);
        }

        $this->info("전체 사용자의 등급 갱신이 완료되었습니다.");
    }
}