<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ItemDetailImage; 

class ItemDetailImageController extends Controller
{
    protected $uploadFolder = 'item_detail_img'; // 상세 이미지 저장 폴더

    public function store(Request $request)
    {
        // 1. 유효성 검사: item_id는 필수, img_path는 배열이며, 각 요소가 이미지 파일인지 확인
        $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'img_path' => 'required|array', // 배열이어야 함
            'img_path.*' => 'image|mimes:jpeg,png,jpg,gif,webp', // 배열의 각 요소(파일) 검사
        ]);

        $item_id = $request->input('item_id');

        // 2. 파일 업로드 및 DB 저장
        
        // request에 img_path 파일 배열이 있는지 확인
        if ($request->hasFile('img_path')) {
            
            // 배열로 들어온 모든 파일에 대해 반복
            foreach ($request->file('img_path') as $file) {
                
                if ($file->isValid()) {
                    
                    // A. 파일명 생성 및 저장 (충돌 방지를 위해 hashName() 권장)
                    $file_name = $file->hashName(); 
                    
                    // 'item_detail_img' 폴더에 저장합니다.
                    $file->storeAs($this->uploadFolder, $file_name, 'public'); 

                    // B. DB에 저장
                    ItemDetailImage::create([
                        'item_id' => $item_id,
                        'img_path' => $file_name,
                        // 'sort' 필드가 있다면 여기에 추가
                    ]);
                }
            }
        }
        // 3. 제품 상세 페이지로 리다이렉트 (item.show 라우트가 있다고 가정)
        return redirect()->route('item.show', $item_id)->with('success', '상세 이미지가 성공적으로 업로드되었습니다.');
    }

    // 상세 이미지 파일 및 DB 레코드 삭제
    public function destroy(string $id)
    {
        $detailImage = ItemDetailImage::find($id);

        if ($detailImage) {
            $item_id = $detailImage->item_id;

            // 1. 파일 삭제
            Storage::disk('public')->delete('detail_img/' . $detailImage->img_path);
            
            // 2. DB 레코드 삭제
            $detailImage->delete();

            return redirect()->route('item.show', $item_id)
                             ->with('success', '상세 이미지가 삭제되었습니다.');
        }

        return redirect()->back()->with('error', '상세 이미지를 찾을 수 없습니다.');
    }
}