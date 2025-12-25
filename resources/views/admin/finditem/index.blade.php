@extends('admin.main_nomenu')
@section('content')

<h3 class="alert mt-3 ctg-admin" role="alert">
    제품 선택
</h3>

<script>
function find_text() {
    form1.action="{{ route('finditem.index') }}";
    form1.submit();
}

function send_item(id, name, price){
    if(opener && !opener.closed){
        opener.form1.item_id.value = id;
        opener.form1.item_name.value = name;
        opener.form1.price.value = price;
        opener.form1.prices.value = Number(price) * (Number(opener.form1.numo?.value) || 1);
        self.close();
    }
}
</script>

{{-- 검색폼 --}}
<form name="form1" action="" class="mb-3">
    <div class="row">
        <div class="col-md-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text">이름</span>
                <input type="text" name="text1" value="{{ $text1 }}" placeholder="찾을 이름은?" class="form-control"
                    onkeydown="if (event.keyCode === 13) { find_text(); }">
                <button class="btn mycolor1" type="button" onclick="find_text();">검색</button>
            </div>
        </div>
        <div class="col-md-6 text-end">
            {{-- 필요 시 추가 버튼 가능 --}}
        </div>
    </div>
</form>

{{-- 테이블 --}}
<div class="table-responsive">
    <table class="table table-sm table-bordered table-hover align-middle">
        <thead class="table-secondary text-center">
            <tr>
                <th width="10%">번호</th>
                <th width="20%">분류</th>
                <th width="30%">제품명</th>
                <th width="20%">단가</th>
                <th width="20%">재고</th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $row)
            <tr>
                <td class="text-center">{{ $row->id }}</td>
                <td class="text-center">{{ $row->category_name }}</td>
                <td>
                    <a href="javascript:send_item({{ $row->id }}, '{{ $row->name }}', {{ $row->price }});" 
                       class="text-decoration-none" title="{{ $row->name }}">
                        {{ $row->name }}
                    </a>
                </td>
                <td class="text-end">{{ number_format($row->price) }}</td>
                <td class="text-end">{{ number_format($row->jaego) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">등록된 데이터가 없습니다.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 페이지네이션 --}}
<div class="d-flex justify-content-center mt-3">
    {{ $list->links('mypagination') }}
</div>

@endsection