@extends('main')

@section('content')
<div class="gojeong my-5">

    {{-- 제목 --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold">고객센터</h2>
        <p class="text-muted">궁금한 사항을 빠르게 확인해보세요</p>
    </div>

    {{-- 문의 유형 --}}
    <div class="row text-center mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="cs-box" onclick="filterFaq('delivery', this)">
                <i class="fa-solid fa-truck-fast"></i>
                <p>배송 문의</p>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="cs-box" onclick="filterFaq('exchange', this)">
                <i class="fa-solid fa-rotate-left"></i>
                <p>교환 / 환불</p>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="cs-box" onclick="filterFaq('payment', this)">
                <i class="fa-solid fa-credit-card"></i>
                <p>결제 문의</p>
            </div>
        </div>

        <div class="col-md-3 col-6 mb-3">
            <div class="cs-box" onclick="filterFaq('member', this)">
                <i class="fa-solid fa-user"></i>
                <p>회원 정보</p>
            </div>
        </div>
    </div>

    {{-- FAQ --}}
    <div id="faq" class="mb-5">
        <h5 class="fw-bold mb-3">자주 묻는 질문</h5>

        <div class="accordion" id="faqAccordion">

            {{-- 1. 배송 (4개) --}}
            <div class="accordion-item" data-type="delivery">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq1">
                        [배송] 주문 후 배송은 얼마나 걸리나요?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        온라인몰에서 주문하신 상품은 주문일 기준 3일~5일 이내(주말 및 공휴일 제외)로 배송됩니다.<br><br>
                        ※ 도서산간, 제주도 지역의 경우 5~7일 이내 배송됩니다.<br>
                        ※ 브랜드 세일 기간에는 주문량 폭주로 인해 주문일로부터 5~7일 이내 배송될 수 있습니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="delivery">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                        [배송] 배송지 주소를 변경하고 싶어요.
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        [주문/배송조회] 메뉴에서 주문상태가 '결제완료'인 경우에만 직접 주소 변경이 가능합니다.<br>
                        '상품준비중' 단계부터는 이미 송장 출력 및 패킹이 시작되어 주소 변경이 어려우니 고객센터로 문의 부탁드립니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="delivery">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">
                        [배송] 운송장 번호는 어디서 확인하나요?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        마이페이지 > [주문/배송조회]에서 각 주문별 운송장 번호를 확인하실 수 있습니다.<br>
                        상품 출고 후 택배사 시스템에 등록되기까지 약 반나절에서 하루 정도 시간이 소요될 수 있습니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="delivery">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq4">
                        [배송] 택배사는 어디인가요?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        저희 쇼핑몰은 CJ대한통운을 기본 택배사로 이용하고 있습니다.<br>
                        다만, 예약배송이나 업체직송 상품의 경우 다른 택배사를 이용할 수 있으며 이는 상세페이지 및 알림톡을 통해 안내해 드립니다.
                    </div>
                </div>
            </div>

            {{-- 2. 교환/환불 (5개) --}}
            <div class="accordion-item" data-type="exchange">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq5">
                        [교환/환불] 교환&반품도 매장에서 할 수 있나요?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        온라인 주문 상품의 매장 교환은 어려우며, 온라인몰을 통한 신청이 필요합니다.<br><br>
                        [마이페이지 > 주문/배송조회 > 교환/반품 신청]을 이용해 주세요.<br>
                        ※ 단, 일부 반품 가능 매장에 한해 직접 방문 반품이 가능하오니 '가능매장찾기'를 확인해 주시기 바랍니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="exchange">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq6">
                        [교환/환불] 교환/반품 가능 기간은 언제까지인가요?
                    </button>
                </h2>
                <div id="faq6" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        단순 변심의 경우 상품 수령일로부터 7일 이내에 신청 가능합니다.<br>
                        포장을 뜯었거나 상품의 가치가 훼손된 경우(택 제거, 오염 등)에는 교환 및 반품이 불가하오니 유의해 주세요.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="exchange">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq7">
                        [교환/환불] 반품 배송비는 얼마인가요?
                    </button>
                </h2>
                <div id="faq7" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        단순 변심으로 인한 반품 시 왕복 배송비 6,000원이 부과됩니다.<br>
                        상품 불량이나 오배송으로 인한 경우에는 배송비가 부과되지 않으며 전액 무료로 처리해 드립니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="exchange">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq8">
                        [교환/환불] 환불은 언제 처리되나요?
                    </button>
                </h2>
                <div id="faq8" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        물류센터로 상품이 회수된 후 검수 과정을 거쳐 환불이 진행됩니다.<br>
                        검수 완료 후 신용카드는 카드사 사정에 따라 3~5영업일, 무통장 입금은 익일(공휴일 제외) 이내에 처리됩니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="exchange">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq9">
                        [교환/환불] 증정 사은품도 함께 반품해야 하나요?
                    </button>
                </h2>
                <div id="faq9" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        네, 상품 반품 시 받으셨던 증정 사은품도 반드시 동봉해 주셔야 합니다.<br>
                        사은품이 누락되거나 사용된 경우 환불 금액에서 해당 사은품의 가격이 차감될 수 있습니다.
                    </div>
                </div>
            </div>

            {{-- 3. 결제 (4개) --}}
            <div class="accordion-item" data-type="payment">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq10">
                        [결제] 어떤 결제 수단을 사용할 수 있나요?
                    </button>
                </h2>
                <div id="faq10" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        신용카드, 체크카드, 실시간 계좌이체, 가상계좌(무통장), 간편결제(카카오페이, 네이버페이, 페이코 등)를 이용하실 수 있습니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="payment">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq11">
                        [결제] 영수증(현금영수증) 발급은 어디서 하나요?
                    </button>
                </h2>
                <div id="faq11" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        현금성 결제(실시간 계좌이체, 무통장입금) 시 주문 단계에서 신청 가능합니다.<br>
                        이미 주문을 완료하신 경우 [마이페이지 > 주문내역 상세] 페이지에서 직접 영수증을 출력하실 수 있습니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="payment">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq12">
                        [결제] 결제가 실패했어요.
                    </button>
                </h2>
                <div id="faq12" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        카드 한도 초과, 할부 불가 카드 여부, 브라우저 팝업 차단 여부를 확인해 주세요.<br>
                        지속적으로 실패할 경우 고객센터 혹은 결제 대행사(PG사)에 문의하시면 빠른 처리가 가능합니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="payment">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq13">
                        [결제] 포인트와 쿠폰은 동시에 사용 가능한가요?
                    </button>
                </h2>
                <div id="faq13" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        네, 쿠폰 혜택을 적용하신 후 잔여 금액에 대해 보유하신 포인트를 사용하여 복합 결제가 가능합니다.<br>
                        다만, 일부 특정 프로모션 상품의 경우 중복 사용이 제한될 수 있습니다.
                    </div>
                </div>
            </div>

            {{-- 4. 회원 (3개) --}}
            <div class="accordion-item" data-type="member">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq14">
                        [회원] 아이디/비밀번호를 잊어버렸어요.
                    </button>
                </h2>
                <div id="faq14" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        로그인 페이지 하단의 [아이디/비밀번호 찾기] 버튼을 이용해 주세요.<br>
                        가입 시 등록한 휴대전화 번호 또는 이메일을 통한 본인 인증 후 확인 가능합니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="member">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq15">
                        [회원] 회원 등급 혜택은 무엇인가요?
                    </button>
                </h2>
                <div id="faq15" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        구매 금액에 따라 브론즈, 실버, 골드 등급으로 나뉩니다.<br>
                        등급별로 상시 할인율과 매월 지급되는 쿠폰 팩이 다르며, 자세한 혜택은 [마이페이지 > 회원 등급 안내]에서 확인하실 수 있습니다.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-type="member">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq16">
                        [회원] 회원 탈퇴는 어떻게 하나요?
                    </button>
                </h2>
                <div id="faq16" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        [마이페이지 > 회원정보 수정] 페이지 하단의 [회원 탈퇴] 메뉴를 통해 신청 가능합니다.<br>
                        탈퇴 시 보유하신 포인트와 쿠폰은 모두 소멸되며, 재가입 시 복구가 불가하오니 신중하게 결정해 주세요.
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    {{-- 하단 안내 --}}
    <div class="cs-info text-center">
        <p class="mb-1 fw-bold">고객센터 운영시간</p>
        <p class="text-muted mb-1">평일 09:00 ~ 18:00 (주말/공휴일 휴무)</p>
        <p class="text-muted">☎ 1588-0000</p>
    </div>

</div>

{{-- JS --}}
<script>
function filterFaq(type, el) {
    const items = document.querySelectorAll('.accordion-item');
    const boxes = document.querySelectorAll('.cs-box');

    // 상단 버튼 active
    boxes.forEach(box => box.classList.remove('active'));
    el.classList.add('active');

    let visibleItems = [];

    items.forEach(item => {
        const isMatch = item.dataset.type === type;
        const collapseEl = item.querySelector('.accordion-collapse');

        // 🔥 Bootstrap 인스턴스로 무조건 닫기
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, {
            toggle: false
        });
        bsCollapse.hide();

        // radius용 클래스 초기화
        item.classList.remove('is-first', 'is-last');

        if (isMatch) {
            item.classList.remove('d-none');
            visibleItems.push(item);
        } else {
            item.classList.add('d-none');
        }
    });

    // 🔥 보이는 것 기준으로 첫/마지막 지정
    if (visibleItems.length > 0) {
        visibleItems[0].classList.add('is-first');
        visibleItems[visibleItems.length - 1].classList.add('is-last');
    }
}
</script>

@endsection