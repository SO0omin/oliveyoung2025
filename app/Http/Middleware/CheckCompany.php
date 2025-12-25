<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        // company_id 세션 없으면 로그인으로 리다이렉트
        if (!session()->has('company_id')) {
            return redirect()->route('admin.login')->with('error', '로그인이 필요합니다.');
        }
        return $next($request);
    }
}
