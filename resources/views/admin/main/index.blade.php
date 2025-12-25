@extends('admin.main')
@section('content')

<h3 class="h3 mb-4 text-gray-800">📊 기간별 매출현황 대시보드</h3>

<script>
    function find_text()
    {
        // 폼 액션 수정: admin.main이 아니라 현재 페이지 (chart.index)로 보내는 것이 더 일반적입니다.
        // 현재 라우트 이름을 모를 경우, 임시로 chart.index를 사용합니다.
        form1.action="{{ route('admin.main') }}"; 
        // 만약 관리자 메인 페이지 라우트가 맞다면: form1.action="{{ route('admin.main') }}";
        form1.submit();
    }

    $(function(){
        // ... (datetimepicker 및 setTimeout 코드는 그대로 유지)
        $('#text1').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD',
            defaultDate: '{{ $text1 }}' 
        });
        $('#text2').datetimepicker({
            locale: 'ko',
            format: 'YYYY-MM-DD',
            defaultDate: '{{ $text2 }}'
        });

        setTimeout(function() {
            $('#text1').on('dp.change', function(e){
                find_text();
            });
            $('#text2').on('dp.change', function(e){
                find_text();
            });
        }, 100); 
    });
</script>
<form name="form1" action="">
    <div class="d-flex align-items-center mb-3 justify-content-between">
        <div class="d-flex align-items-center">
            <div class="input-group input-group-sm date me-2" id="text1" style="min-width: 180px;">
                <span class="input-group-text">날짜</span>
                <input type="text" class="form-control" size="10" name="text1" value="{{ $text1 }}"
                onKeydown="if (event.keyCode == 13) { find_text(); }">
                <div class="input-group-text">
                    <span class="input-group-addon">
                        <i class="far fa-calendar-alt fa-lg"></i>
                    </span>
                </div>
            </div>
            <span class="me-2">-</span>
            <div class="input-group input-group-sm date" id="text2" style="min-width: 150px;">
                <input type="text" class="form-control" size="10" name="text2" value="{{ $text2 }}"
                onKeydown="if (event.keyCode == 13) { find_text(); }">
                <div class="input-group-text">
                    <span class="input-group-addon">
                        <i class="far fa-calendar-alt fa-lg"></i>
                    </span>
                </div>
            </div>
        </div>
        </div>
</form>

<hr>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-main shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold ctg-admin text-uppercase mb-1">
                            💰 총 매출액 ({{ date('m/d', strtotime($text1)) }} ~ {{ date('m/d', strtotime($text2)) }})
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ isset($total_sales_amount) ? number_format($total_sales_amount) : '0' }} 원
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-main shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold ctg-admin text-uppercase mb-1">
                            📦 총 판매 건수
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ isset($total_sales_count) ? number_format($total_sales_count) : '0' }} 건
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card border-left-main shadow h-100 py-2">
            <div class="card-body">
                 <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold ctg-admin text-uppercase mb-1">
                            ℹ️ 대시보드 정보
                        </div>
                        <div class="text-sm text-gray-600">
                            기간 선택 후 자동으로 새로고침되며, 하단의 표와 차트는 선택된 기간에 맞춰 데이터를 표시합니다.
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row mb-5">
    
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold ctg-admin">🍩 종류별 판매 수량 분포도</h6>
            </div>
            <div class="card-body text-center">
                <script src="{{ asset('static/my/js/chart.min.js') }}"></script>
                <div style="width: 100%; max-width: 450px; height: 350px; margin: 0 auto;"> 
                    <canvas id="chartDistribution"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold ctg-admin">🥇 BEST 10 제품 (수량 기준)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-sm table-bordered table-hover table-striped">
                        <thead class="table-secondary">
                            <tr>
                                <th width="70%">제품명</th>
                                <th width="30%" class="text-end">매출건수</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list_best as $row)
                            <tr>
                                <td>{{ $row->item->name }}</td>
                                <td class="text-end">{{ number_format($row->cnumo) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold ctg-admin">📅 월별 제품별 매출현황 ({{ $year }}년)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                    <table class="table table-sm table-bordered table-hover table-striped">
                        <thead class="table-secondary sticky-top">
                            <tr>
                                <th style="min-width: 150px;">제품명</th>
                                @for($m=1;$m<=12;$m++)
                                    <th class="text-center" style="min-width: 40px;">{{ $m }}월</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list_crosstab as $row)
                            <tr>
                                <td>{{ $row->item->name }}</td>
                                @for($m=1;$m<=12;$m++)
                                    @php $val = $row->{'s'.$m} @endphp
                                    <td class="text-end">{{ $val==0?'':number_format($val) }}</td>
                                @endfor
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('chartDistribution').getContext('2d');

// 1. PHP에서 넘어온 레이블 문자열을 JavaScript 배열로 변환
const rawLabels = "[{!! $str_label !!}]"; 
const labelsArray = JSON.parse(rawLabels.replace(/'/g, '"'));

// 2. 최대 길이 설정 및 줄임표 처리 함수
const MAX_LABEL_LENGTH = 15; 

const truncatedLabels = labelsArray.map(label => {
    if (label.length > MAX_LABEL_LENGTH) {
        return label.substring(0, MAX_LABEL_LENGTH) + '...';
    }
    return label;
});


new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: truncatedLabels, 
        datasets: [{
            data: [{{ $str_data }}], 
            backgroundColor: [ 
                "#4e73df", // Primary Blue
                "#1cc88a", // Success Green
                "#36b9cc", // Info Cyan
                "#f6c23e", // Warning Yellow
                "#e74a3b", // Danger Red
                "#858796", // Secondary Gray
                "#5a5c69"  // Dark Gray
            ],
            hoverBackgroundColor: [
                "#2e59d9",
                "#17a673",
                "#2c9faf",
                "#f4b619",
                "#d43b2f",
                "#737583",
                "#4d4f5c"
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
            label: '판매 수량 분포' 
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, 
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    font: { size: 12 },
                    padding: 15
                }
            },
            tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#000000",   // body 글자색
                titleColor: "#000000",  // title 글자색
                borderColor: '#dddfeb',
                borderWidth: 1,
                cornerRadius: 3,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        let originalLabel = labelsArray[context.dataIndex] || ''; 
                        let currentValue = context.raw;
                        let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((currentValue / sum) * 100).toFixed(1) + '%';
                        
                        return originalLabel + ': ' + number_format(currentValue) + ' (' + percentage + ')';
                    }
                }
            }
        }
    }
});

// number_format 함수 추가 (JS에서 숫자에 콤마를 찍기 위함)
function number_format(number) {
    return new Intl.NumberFormat('ko-KR').format(number);
}
</script>
@endsection