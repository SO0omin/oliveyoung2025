<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Gubun;
use App\Models\Sale;

require "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GiganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $text1 = request('text1');
        if(!$text1) $text1 = date("Y-m-d",strtotime("-1 month")); //오늘기준 1달전 날짜

        $text2 = request('text2');
        if(!$text2) $text2 = date("Y-m-d"); //텍스트가 null이면 오늘 날짜

        $text3 = request('text3');
        if(!$text3) $text3 = 0; //전체제품

        $data['text1'] = $text1;
        $data['text2'] = $text2;
        $data['text3'] = $text3;
        $data['list'] = $this->getlist($text1, $text2, $text3); //배열 선언 + 값할당

        $data['list_item'] = $this->getlist_item(); //콤보상자용 제품정보
        return view('admin.gigan.index', $data);
    }

    public function getlist($text1, $text2, $text3)
    {
        $company_id = session('company_id');

        return Sale::leftjoin('items', 'sales.item_id', '=', 'items.id')
            ->select('sales.*', 'items.name as item_name')
            ->when($company_id != 1, function($q) use ($company_id) {
                $q->where('items.company_id', $company_id);
            })
            ->whereBetween('sales.writeday', [$text1, $text2])
            // text3가 0이 아닐 때만 item_id 조건을 추가
            ->when($text3 != 0, function($q) use ($text3) {
                $q->where('sales.item_id', $text3);
            })
            ->orderBy('sales.writeday', 'asc')
            ->paginate(10)
            ->appends(request()->all()); // 모든 요청 파라미터를 자동으로 유지
    }

    public function getlist_item(){
        $company_id = session('company_id');
        $result = Item::orderby('name')->when($company_id != 1, function($query) use ($company_id) { $query->where('company_id', $company_id);})->get();
        return $result;
    }

    public function getlist_all($text1, $text2, $text3)
    {
        if($text3 == 0)
            $result = Sale::leftjoin('items','sales.item_id','=','items.id')->
                select('sales.*','items.name as item_name')->
                wherebetween('sales.writeday',array($text1, $text2))->
                orderby('sales.id','desc')->get();
        else
            $result = Sale::leftjoin('items','sales.item_id','=','items.id')->
                select('sales.*','items.name as item_name')->
                wherebetween('sales.writeday',array($text1, $text2))->
                where('sales.item_id','=', $text3)->
                orderby('sales.id','desc')->get();

        return $result;
        // $sql = 'SELECT * FROM members ORDER BY name';
        // return DB::select($sql); //DB 파사드를 활용
    }

    public function excel() {
        $text1 = request('text1');
        $text2 = request('text2');
        $text3 = request('text3');

        $list = $this->getlist_all($text1, $text2, $text3);

        // ✅ 문서 객체와 시트 객체 분리
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 칼럼 너비
        $sheet->getColumnDimension("A")->setWidth(12);
        $sheet->getColumnDimension("B")->setWidth(25);
        $sheet->getColumnDimension("C")->setWidth(12);
        $sheet->getColumnDimension("D")->setWidth(12);
        $sheet->getColumnDimension("E")->setWidth(12);
        $sheet->getColumnDimension("F")->setWidth(12);
        $sheet->getColumnDimension("G")->setWidth(12);

        // 정렬
        $sheet->getStyle("A")->getAlignment()->setHorizontal("center");
        $sheet->getStyle("B")->getAlignment()->setHorizontal("left");
        $sheet->getStyle("C:F")->getAlignment()->setHorizontal("right");
        $sheet->getStyle("G")->getAlignment()->setHorizontal("left");

        // 제목
        $sheet->setCellValue("A1", "매출입장");
        $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true);

        // 기간
        $sheet->setCellValue("G1", "기간: " . $text1 . " - " . $text2);
        $sheet->getStyle("G1")->getAlignment()->setHorizontal("right");

        // 헤더
        $sheet->getStyle("A2:G2")->getAlignment()->setHorizontal("center");
        $sheet->getStyle("A2:G2")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCCCC');
        $sheet->setCellValue("A2", "날짜")
            ->setCellValue("B2", "제품명")
            ->setCellValue("C2", "단가")
            ->setCellValue("D2", "매입수량")
            ->setCellValue("E2", "매출수량")
            ->setCellValue("F2", "금액")
            ->setCellValue("G2", "비고");

        // 데이터
        $i = 3;
        foreach ($list as $row) {
            $sheet->setCellValue("A$i",$row->writeday)
                ->setCellValue("B$i",$row->item_name)
                ->setCellValue("C$i",$row->price ?: "")
                ->setCellValue("D$i",$row->numi ?: "")
                ->setCellValue("E$i",$row->numo ?: "")
                ->setCellValue("F$i",$row->prices ?: "")
                ->setCellValue("G$i",$row->bigo);
            $i++;
        }

        // 다운로드
        $fname = "매출입장($text1 - $text2).xlsx";
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment;filename=\"$fname\"");
        header("Cache-Control: max-age=0");

        ob_end_clean(); // 출력 버퍼 제거 (파일 깨짐 방지)
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save("php://output");
        exit;
    }
}
