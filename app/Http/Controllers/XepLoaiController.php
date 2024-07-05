<?php

namespace App\Http\Controllers;

use App\Models\XepLoai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class XepLoaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:xem danh sách xếp loại', ['only' => ['index']]);
        $this->middleware('permission:tạo xếp loại', ['only' => ['create', 'store']]);
        $this->middleware('permission:cập nhật xếp loại', ['only' => ['update', 'edit']]);
        $this->middleware('permission:xem xếp loại', ['only' => ['show']]);
        //$this->middleware('permission:xóa xếp loại', ['only' => ['destroy']]);
    }

    //Hiển thị danh sách Xếp loại
    public function index()
    {
        $xep_loai = XepLoai::orderBy('diem_toi_thieu', 'DESC')->get();

        return view('xeploai.index', ['xep_loai' => $xep_loai]);
    }


    //Tạo mới Xếp loại
    public function create()
    {
        return view('xeploai.create');
    }


    //Lưu trữ thông tin Xếp loại
    public function store(Request $request)
    {
        //Kiểm tra thông tin đầu vào
        $validated = $request->validate([
            'ma_xep_loai' => 'required|unique:App\Models\XepLoai,ma_xep_loai',
            'ten_xep_loai' => 'required',
            'diem_toi_thieu' => 'required',
        ]);

        $xep_loai = new XepLoai();
        $xep_loai->ma_xep_loai = $request->ma_xep_loai;
        $xep_loai->ten_xep_loai = $request->ten_xep_loai;
        $xep_loai->diem_toi_thieu = $request->diem_toi_thieu;
        $xep_loai->save();

        return redirect()->route('xeploai.edit', ['id' => $request->ma_xep_loai])->with('message', 'Đã tạo mới Xếp loại thành công');
    }


    //Sửa thông tin Xếp loại
    public function edit($id)
    {
        $xep_loai = XepLoai::where('ma_xep_loai', $id)->first();

        return view('xeploai.edit', [
            'xep_loai' => $xep_loai,
        ]);
    }


    //Cập nhật thông tin Xếp loại
    public function update(Request $request, $id)
    {
        //Kiểm tra thông tin đầu vào
        $validated = $request->validate([
            'ten_xep_loai' => 'required',
            'diem_toi_thieu' => 'required',
        ]);

        $xep_loai = XepLoai::where('ma_xep_loai', $id)->first();
        $xep_loai->ten_xep_loai = $request->ten_xep_loai;
        $xep_loai->diem_toi_thieu = $request->diem_toi_thieu;
        $xep_loai->save();
        return redirect()->route('xeploai.edit', ['id' => $xep_loai->ma_xep_loai])->with('message', 'Đã cập nhật Xếp loại thành công');
    }
}
