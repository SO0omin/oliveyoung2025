@extends('main')
@section('content')

<div class="container-fluid main-header-cart mb-3">
    <div class="gojeong main-header-content">
        <h2>주문/결제</h2>
        @if ($isFromCart)
        <span>
            <p>01.장바구니</p> ><strong> 02.주문/결제 </strong><p> > 03. 주문 완료 </p>
        </span>
        @else
         <span>
            <strong>01.주문/결제 </strong><p> > 02. 주문 완료 </p>
        </span>
        @endif
    </div>
</div>
@php
    $hasAddress = count($addresses) > 0;
@endphp
<div class="gojeong">
    {{-- 배송지 정보 --}}
    <div class="card mb-3 p-3">

        <h5 class="mb-3">배송지 정보</h5>

        {{-- 배송지 정보 표 --}}
        <table class="table table-bordered align-middle" id="address-table">
            <tbody>
                <tr>
                    <th>배송지 선택</th>
                    <td>
                        <label class="me-3">
                            <input type="radio" id="radio-existing" name="address_option" value="existing"
                                {{ $hasAddress ? '' : 'disabled' }} checked>
                            기존 배송지 사용
                        </label>

                        <label>
                            <input type="radio" id="radio-new" name="address_option" value="new"
                                {{ $hasAddress ? '' : 'checked' }}>
                            새로운 배송지 입력
                        </label>

                        {{-- 기존 배송지 셀렉트 --}}
                        <select id="existing-address-select"
                                class="form-select mb-3"
                                {{ $hasAddress ? '' : 'style=display:none;' }}>
                            @foreach($addresses as $addr)
                                <option value="{{ $addr->id }}"
                                    data-label="{{ $addr->label }}"
                                    data-name="{{ $addr->name }}"
                                    data-phone="{{ $addr->phone }}"
                                    data-zipcode="{{ $addr->zipcode }}"
                                    data-address1="{{ $addr->address1 }}"
                                    data-address2="{{ $addr->address2 }}"
                                >
                                    {{ $addr->name }} / {{ $addr->address1 }} {{ $addr->address2 }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                <tr>
                    <th style="width: 150px;">배송지명</th>
                    <td>
                        <input type="text" name="label" id="label" class="form-control">
                    </td>
                </tr>

                <tr>
                    <th>받는 사람</th>
                    <td>
                        <input type="text" name="name" id="name" class="form-control">
                    </td>
                </tr>

                <tr>
                    <th>전화번호</th>
                    <td>
                        <input type="text" name="phone" id="phone" class="form-control">
                    </td>
                </tr>

                <tr>
                    <th>우편번호</th>
                    <td>
                        <div class="input-group" style="width: 250px;">
                            <input type="text" name="zipcode" id="zipcode" class="form-control" readonly>
                            <button type="button" class="btn btn-outline-secondary" onclick="execDaumPostcode()">주소 검색</button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>주소</th>
                    <td>
                        <input type="text" name="address1" id="address1" class="form-control" readonly>
                    </td>
                </tr>

                <tr>
                    <th>상세주소</th>
                    <td>
                        <input type="text" name="address2" id="address2" class="form-control">
                    </td>
                </tr>
            </tbody>
        </table>

    </div>   
    <div class="card shadow-sm">
        <div class="card-header mycolor2 text-white">
            <h3 class="mb-0">결제 페이지</h3>
        </div>
        <div class="card-body">
            @php $total = 0; @endphp
            @foreach($carts as $cart)
                @php
                    $price = $cart->item->activeDiscount 
                                ? $cart->item->activeDiscount->sale_price * $cart->qty
                                : $cart->item->price * $cart->qty;
                    $total += $price;
                @endphp
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('storage/item_img/' . $cart->item->pic) }}" 
                         style="width:100px; height:auto; object-fit:cover;" class="me-3 border rounded">
                    <div>
                        <h5 class="mb-1">{{ $cart->item->name }}</h5>
                        <p class="mb-0 item-price">
                            @if($cart->item->activeDiscount)
                                <del style="color:gray;">{{ number_format($cart->item->price * $cart->qty) }}원</del>
                                <strong style="color:#e74c3c;">{{ number_format($cart->item->activeDiscount->sale_price * $cart->qty) }}원</strong>
                            @else
                                <strong>{{ number_format($cart->item->price * $cart->qty) }}원</strong>
                            @endif
                        </p>
                        <p>수량: {{ $cart->qty }}</p>
                    </div>
                </div>
                <hr>
            @endforeach

            {{-- 💡 [추가] 포인트 사용 영역 --}}
            <div class="card p-3 mb-3 border">
                <h5 class="mb-3">포인트 사용</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>사용 가능 포인트:</span>
                    <strong id="available-points">{{ number_format($customerPoints) }}</strong>
                </div>

                <div class="input-group mb-2">
                    <input type="number" id="use-points" name="use_points" class="form-control" placeholder="사용할 포인트 입력 (최대 {{ number_format($customerPoints) }})" min="0" max="{{ $customerPoints }}" value="0">
                    <button type="button" class="btn btn-outline-secondary" id="use-all-points">모두 사용</button>
                </div>
                <small class="text-danger" id="point-error" style="display:none;">사용 가능한 포인트를 초과했습니다.</small>
            </div>

            <div class="alert alert-success text-end mt-3 mb-3">
                <h5>
                    최종 결제 금액: <strong id="final-total-price-display">{{ number_format($total) }}</strong>원
                </h5>
            </div>
            {{-- ... 기존 상품 목록 및 총 결제금액 표시 이전 ... --}}

            <form method="POST" action="{{ route('checkout.pay') }}" id="final-pay-form">
                @csrf

                {{-- 배송 정보 hidden 필드 (JS에서 동기화) --}}
                <input type="hidden" name="label" id="hidden_label">
                <input type="hidden" name="name" id="hidden_name">
                <input type="hidden" name="phone" id="hidden_phone">
                <input type="hidden" name="zipcode" id="hidden_zipcode">
                <input type="hidden" name="address1" id="hidden_address1">
                <input type="hidden" name="address2" id="hidden_address2">
                <input type="hidden" name="address_id" id="address_id">

                {{-- 상품 정보 처리 --}}
                @if($isFromCart)
                    {{-- 장바구니에서 온 경우 --}}
                    @foreach($carts as $cart)
                        <input type="hidden" name="cart_ids[]" value="{{ $cart->id }}">
                    @endforeach
                @else
                    {{-- 바로구매(buyNow)에서 온 경우: 확실하게 단일 아이템 정보 주입 --}}
                    <input type="hidden" name="item_id" value="{{ $carts->first()->item->id }}">
                    <input type="hidden" name="qty" value="{{ $carts->first()->qty }}">
                @endif

                <input type="hidden" name="final_total_price" id="final_total_price" value="{{$total}}">
                <input type="hidden" name="points_used" id="points_used" value="0"> 
                
                <button type="submit" class="btn mycolor1 w-100 mt-3">결제하기</button>
            </form>
        </div>
    </div>
</div>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const hasAddress = {{ $hasAddress ? 'true' : 'false' }};
        const radioExisting = document.getElementById("radio-existing");
        const radioNew = document.getElementById("radio-new");

        const select = document.getElementById("existing-address-select");
        const addressIdInput = document.getElementById("address_id");

        const fields = {
            label: document.getElementById("label"),
            name: document.getElementById("name"),
            phone: document.getElementById("phone"),
            zipcode: document.getElementById("zipcode"),
            address1: document.getElementById("address1"),
            address2: document.getElementById("address2"),
        };
        const originalTotal = {{ $total }};
        const availablePoints = {{ $customerPoints }}; // 컨트롤러에서 받아온 값
        const usePointsInput = document.getElementById("use-points");
        const useAllPointsButton = document.getElementById("use-all-points");
        const finalTotalPriceDisplay = document.getElementById("final-total-price-display");
        const finalTotalPriceInput = document.getElementById("final_total_price");
        const pointsUsedInput = document.getElementById("points_used");
        const pointError = document.getElementById("point-error");

        // --------------------------------------------------------------------
        // 기존 주소 자동 채우기
        // --------------------------------------------------------------------
        function fillAddress() {
            const opt = select.options[select.selectedIndex];

            addressIdInput.value = opt.value;

            fields.label.value = opt.dataset.label;
            fields.name.value = opt.dataset.name;
            fields.phone.value = opt.dataset.phone;
            fields.zipcode.value = opt.dataset.zipcode;
            fields.address1.value = opt.dataset.address1;
            fields.address2.value = opt.dataset.address2;
        }

        // --------------------------------------------------------------------
        // 신규 주소 필드 비우기
        // --------------------------------------------------------------------
        function clearAddress() {
            Object.values(fields).forEach(f => f.value = "");
            addressIdInput.value = "";
        }

        // --------------------------------------------------------------------
        // 신규 주소 저장 (AJAX)
        // --------------------------------------------------------------------
        async function createAddress() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            const response = await fetch("{{ route('address.add') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf
                },
                body: JSON.stringify({
                    label: fields.label.value,
                    name: fields.name.value,
                    phone: fields.phone.value,
                    zipcode: fields.zipcode.value,
                    address1: fields.address1.value,
                    address2: fields.address2.value,
                }),
            });

            return await response.json();
        }

        // --------------------------------------------------------------------
        // 제출 시: 신규 주소면 먼저 주소 생성 → form 제출
        // --------------------------------------------------------------------
        document.getElementById("final-pay-form").addEventListener("submit", async function (e) {
            e.preventDefault();

            // 1. 현재 화면의 input 값을 hidden 필드로 강제 복사하는 함수
            const syncAddress = () => {
                document.getElementById("hidden_label").value = document.getElementById("label").value;
                document.getElementById("hidden_name").value = document.getElementById("name").value;
                document.getElementById("hidden_phone").value = document.getElementById("phone").value;
                document.getElementById("hidden_zipcode").value = document.getElementById("zipcode").value;
                document.getElementById("hidden_address1").value = document.getElementById("address1").value;
                document.getElementById("hidden_address2").value = document.getElementById("address2").value;
            };

            const radioNew = document.getElementById("radio-new");

            if (radioNew.checked) {
                // 새 주소 입력 모드라면 AJAX 저장 먼저 시도
                try {
                    const result = await createAddress();
                    if (result.status === "success" || result.address_id) {
                        document.getElementById("address_id").value = result.address_id;
                    }
                } catch (error) {
                    console.error("주소 저장 중 오류:", error);
                }
            }

            // 2. 제출 직전에 다시 한번 동기화 (값이 비어있는 것 방지)
            syncAddress();

            // 3. 진짜 제출
            this.submit();
        });

        // --------------------------------------------------------------------
        // 초기 상태 설정
        // --------------------------------------------------------------------
        if (!hasAddress) {
            radioNew.checked = true;
            clearAddress();
            select.disabled = true;
            return;
        }

        radioExisting.addEventListener("change", () => {
            select.disabled = false;
            fillAddress();
        });

        radioNew.addEventListener("change", () => {
            select.disabled = true;
            clearAddress();
        });

        select.addEventListener("change", fillAddress);

        fillAddress();
        // --------------------------------------------------------------------
        // 포인트 계산 및 총액 업데이트 함수
        // --------------------------------------------------------------------
        function updateFinalPrice() {
            let points = parseInt(usePointsInput.value) || 0;
            
            // 1. 유효성 검사 (사용 가능 포인트 초과 금지)
            if (points > availablePoints) {
                points = availablePoints;
                usePointsInput.value = availablePoints;
                pointError.style.display = "block";
            } else {
                pointError.style.display = "none";
            }

            // 2. 총 결제 금액 초과 금지 (총액만큼만 포인트 사용 가능)
            if (points > originalTotal) {
                points = originalTotal;
                usePointsInput.value = originalTotal;
            }
            
            // 3. 최종 금액 계산
            const finalPrice = originalTotal - points;

            // 4. UI 업데이트 및 숨김 필드 저장
            finalTotalPriceDisplay.textContent = finalPrice.toLocaleString('ko-KR') + '원';
            finalTotalPriceInput.value = finalPrice;
            pointsUsedInput.value = points;
        }

        // --------------------------------------------------------------------
        // 이벤트 리스너 연결
        // --------------------------------------------------------------------

        // 💡 포인트 입력 필드 변경 이벤트
        usePointsInput.addEventListener("input", updateFinalPrice);

        // 💡 모두 사용 버튼 이벤트
        useAllPointsButton.addEventListener("click", () => {
            // 총액을 초과하지 않는 선에서 사용 가능한 모든 포인트를 입력
            const maxUse = Math.min(availablePoints, originalTotal);
            usePointsInput.value = maxUse;
            updateFinalPrice();
        });

        // --------------------------------------------------------------------
        // 초기 상태: 페이지 로드 시 최종 금액을 한번 업데이트 (0포인트 사용 기준)
        // --------------------------------------------------------------------
        updateFinalPrice(); // 초기값 0으로 설정
    });
    // 카카오 주소 검색 함수
    function execDaumPostcode() {
        // '기존 배송지 사용'이 체크되어 있다면 '새로운 배송지'로 자동 전환해주는 센스!
        const radioNew = document.getElementById("radio-new");
        if (radioNew) {
            radioNew.checked = true;
            // 라디오 버튼 변경 이벤트 강제 발생 (기존의 clearAddress 등이 실행되도록)
            radioNew.dispatchEvent(new Event('change'));
        }

        new daum.Postcode({
            oncomplete: function(data) {
                var addr = ''; // 주소 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져옴
                if (data.userSelectedType === 'R') { // 도로명 주소
                    addr = data.roadAddress;
                } else { // 지번 주소
                    addr = data.jibunAddress;
                }

                // 각 입력창에 값 입력
                document.getElementById('zipcode').value = data.zonecode;
                document.getElementById("address1").value = addr;
                
                // 상세주소 칸으로 포커스 이동 및 기존 값 삭제
                const addr2Field = document.getElementById("address2");
                addr2Field.value = "";
                addr2Field.focus();
            }
        }).open();
    }
</script>
</script>

@endsection