<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;

class KakaoController extends Controller
{
    public function callback(Request $request)
    {
        // 1. 토큰 요청 및 사용자 정보 가져오기 (기존 코드 유지)
        $response = Http::asForm()->post('https://kauth.kakao.com/oauth/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => env('KAKAO_REST_KEY'),
            'client_secret' => env('KAKAO_CLIENT_SECRET'),
            'redirect_uri'  => env('KAKAO_REDIRECT_URI'),
            'code'          => $request->code,
        ]);

        $data = $response->json();
        if (!isset($data['access_token'])) return redirect()->route('login');

        $userResponse = Http::withHeaders(['Authorization' => 'Bearer '.$data['access_token']])
                            ->get('https://kapi.kakao.com/v2/user/me');
        $kakaoUser = $userResponse->json();

        $kakaoId = 'kakao_'.$kakaoUser['id'];
        $nickname = $kakaoUser['properties']['nickname'] ?? '카카오사용자';

        // 2. 가입 여부 확인
        $user = Customer::where('uid', $kakaoId)->first();

        if ($user) {
            // [원하는 것 1] 가입되어 있으면 바로 로그인 처리
            session([
                'id' => $user->id,
                'uid' => $user->uid,
                'name' => $user->name,
                'grade' => $user->grade ?? 'newbie',
            ]);
            return redirect()->route('main.index'); // 메인으로 이동
        } else {
            // [원하는 것 2, 3] 가입 안 되어 있으면 가입창으로 (정보 들고 가기)
            return redirect()->route('register', [
                'uid' => $kakaoId, 
                'name' => $nickname
            ]);
        }
    }
}