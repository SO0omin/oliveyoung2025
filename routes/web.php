<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; // 추가 (CAPTCHA 테스트용)

// 관리자 컨트롤러
use App\Http\Controllers\admin\MainController as AdminMainController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AjaxController;
use App\Http\Controllers\admin\CompanyController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CarouselsController;
use App\Http\Controllers\admin\DiscountController;
use App\Http\Controllers\admin\EventController as AdminEventController;
use App\Http\Controllers\admin\ItemController;
use App\Http\Controllers\admin\ItemDetailImageController;
use App\Http\Controllers\admin\SaleiController;
use App\Http\Controllers\admin\SaleoController;
use App\Http\Controllers\admin\FinditemController;
use App\Http\Controllers\admin\FindcompanyController;
use App\Http\Controllers\admin\FindEventItemController;
use App\Http\Controllers\admin\GiganController;
use App\Http\Controllers\admin\BestController;
use App\Http\Controllers\admin\CrosstabController;
use App\Http\Controllers\admin\ChartController;
use App\Http\Controllers\admin\LoginController as AdminLoginController;
use App\Http\Controllers\admin\PictureController;

// 사용자 컨트롤러
use App\Http\Controllers\MainController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\KakaoController;

// ============================================================
// 1. 관리자(ADMIN) 영역
// ============================================================

// [로그인 전] 미들웨어 제외 영역
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/', [AdminLoginController::class, 'login']);
});

// [로그인 후] 미들웨어 적용 영역
Route::prefix('admin')
    ->middleware('check.company')
    ->group(function () {
        Route::get('login/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::get('main', [AdminMainController::class, 'index'])->name('admin.main');

        Route::get('admin/check-id/',[AdminController::class,'checkId'])->name('admins.check_id');
        
        Route::resource('admins', AdminController::class);
        Route::resource('company', CompanyController::class);

        Route::get('item/jaego', [ItemController::class, 'jaego']);
        Route::resource('item', ItemController::class);
        
        // 상세 이미지 등록 및 삭제
        Route::post('/admin/item_detail_image', [ItemDetailImageController::class, 'store'])->name('item_detail_image.store');
        Route::delete('admin/item_detail_image/{item_detail_image}', [ItemDetailImageController::class, 'destroy'])->name('item_detail_image.destroy');
        
        Route::resource('salei', SaleiController::class);
        Route::resource('saleo', SaleoController::class);
        Route::resource('finditem', FinditemController::class);
        Route::resource('findcompany', FindcompanyController::class);
        Route::resource('discount', DiscountController::class);

        // ADMIN - EVENT 라우트 순서 유지
        Route::get('event', [AdminEventController::class, 'index'])->name('admin.event.index');
        Route::get('event/create', [AdminEventController::class, 'create'])->name('admin.event.create');
        Route::post('event', [AdminEventController::class, 'store'])->name('admin.event.store');
        Route::get('event/{event}', [AdminEventController::class, 'show'])->name('admin.event.show');
        Route::get('event/{event}/edit', [AdminEventController::class, 'edit'])->name('admin.event.edit');
        
        // 추가 이미지 삭제 (순서 중요: 일반 삭제보다 위)
        Route::delete('event/image/{eventImage}', [AdminEventController::class, 'destroyImage'])->name('admin.event.destroyImage');
        Route::put('event/{event}', [AdminEventController::class, 'update'])->name('admin.event.update');
        Route::delete('event/{event}', [AdminEventController::class, 'destroy'])->name('admin.event.destroy');

        Route::get('findeventitem', [FindEventItemController::class, 'index'])->name('admin.findeventitem.index');
        Route::get('gigan/excel', [GiganController::class, 'excel']);
        Route::resource('gigan', GiganController::class);
        Route::resource('carousels', CarouselsController::class);
        Route::resource('picture', PictureController::class);

        // AJAX 라우트
        Route::get('ajax', [AjaxController::class, 'index'])->name('ajax.index'); 
        Route::post('ajax/{type}', [AjaxController::class, 'store'])->name('ajax.store'); 
        Route::put('ajax/{type}/{id}', [AjaxController::class, 'update'])->name('ajax.update'); 
        Route::delete('ajax/{type}/{id}', [AjaxController::class, 'destroy'])->name('ajax.destroy'); 
        
        Route::resource('category', CategoryController::class);
        Route::resource('subcategory', SubCategoryController::class);

        // 카테고리 로드
        Route::get('/get-subcategories/{categoryId}', [CategoryController::class, 'getSubcategories'])->name('admin.subcategories'); 
        Route::get('/get-detailcategories/{subcategoryId}', [CategoryController::class, 'getDetailcategories'])->name('admin.detailcategories');
});

// ============================================================
// 2. 일반 사용자(USER) 영역
// ============================================================

// [로그인 상관없음] 공용 페이지
Route::get('/', [MainController::class, 'index']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// 회원가입
Route::get('/register/check-uid', [LoginController::class, 'checkUid'])->name('register.check_uid');
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register');

Route::resource('main', MainController::class);
Route::resource('customer', CustomerController::class);

// 장바구니
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::resource('cart', CartController::class);
Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.updateQty');

// 카테고리 및 상품 상세
Route::get('/categories/{category_id}', [GoodsController::class, 'categoryIndex'])->name('category.index');
Route::get('/categories/{category_id}/{sub_id}/{detail_id?}', [GoodsController::class, 'subCategoryView'])->name('category.sub');
Route::get('/goods/{id}', [GoodsController::class, 'detail'])->name('goods.detail');

Route::get('/rank/{category_id?}', [GoodsController::class, 'rank'])->name('goods.rank');
Route::get('/discount/{category_id?}', [GoodsController::class, 'discount'])->name('goods.discount');
Route::get('/carousel/{page?}', [CarouselController::class, 'index'])->name('carousel.index');
Route::resource('event', EventController::class);
Route::get('/search', [GoodsController::class, 'search'])->name('goods.search');
Route::view('/customer', 'customer_service')->name('customer');

// [로그인 필수] 마이페이지 및 결제
Route::middleware(['check.customer'])->group(function () {
    // 결제
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::get('/checkout/buyNow', [CheckoutController::class, 'buyNow'])->name('checkout.buyNow');
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');

    // 주문 및 주소
    Route::get('/my/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/my/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/address', [AddressController::class, 'index'])->name('address.index');
    Route::post('/address/add', [AddressController::class, 'add'])->name('address.add');
    Route::post('/address', [AddressController::class, 'store'])->name('address.store');

    // 마이페이지 관리
    Route::get('/my',[MyController::class, 'index'])->name('my.index');
    Route::get('/my/profile/edit', [MyController::class,'edit'])->name('my.profile.edit');
    Route::put('/my/profile/update', [MyController::class, 'update'])->name('my.profile.update');
    Route::delete('/my/profile/withdraw', [MyController::class, 'withdraw'])->name('my.profile.withdraw');
});

// 기타 기능 (CAPTCHA, 카카오)
Route::get('/test-captcha', function () { return view('test_captcha'); })->name('test.captcha');
Route::post('/test-captcha', function (Request $request) {
    $request->validate(['g-recaptcha-response' => 'required|captcha']);
    return 'CAPTCHA 통과!';
})->name('captcha.submit');

Route::get('/kakao/callback', [KakaoController::class, 'callback'])->name('kakao.callback');