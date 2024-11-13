<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\Phong;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:xem danh sách phòng/đội', ['only' => ['index']]);
        $this->middleware('permission:tạo phòng/đội', ['only' => ['create', 'store']]);
        $this->middleware('permission:cập nhật phòng/đội', ['only' => ['update', 'edit']]);
        $this->middleware('permission:xem phòng/đội', ['only' => ['show']]);
        $this->middleware('permission:khóa phòng/đội', ['only' => ['destroy']]);
        $this->middleware('permission:mở khóa phòng/đội', ['only' => ['restore']]);
    }

    // Hiển thị danh sách Phòng/Đội
    public function index()
    {
        $phong = Phong::all();

        return view('phong.index', ['phong' => $phong]);
    }


    // Tạo mới Phòng/Đội
    public function create()
    {
        $don_vi = DonVi::where('ma_trang_thai', 1)->get();

        return view('phong.create', ['don_vi' => $don_vi]);
    }


    // Lưu trữ thông tin Phòng/Đội
    public function store(Request $request)
    {
        // Kiểm tra thông tin đầu vào
        $validated = $request->validate([
            'ma_phong' => 'required|unique:App\Models\Phong,ma_phong',
            'ten_phong' => 'required',
        ]);

        $phong = new Phong();
        $phong->ma_phong = $request->ma_phong;
        $phong->ten_phong = $request->ten_phong;
        $phong->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
        $phong->ma_trang_thai = 1;
        $phong->save();

        return redirect()->route('phong.edit', ['id' => $request->ma_phong])->with('message', 'Đã tạo mới Phòng/Đội thành công');
    }


    // Sửa thông tin Phòng/Đội
    public function edit($id)
    {
        $phong = Phong::where('ma_phong', $id)->first();
        $don_vi = DonVi::all();

        return view('phong.edit', [
            'phong' => $phong,
            'don_vi' => $don_vi
        ]);
    }


    // Cập nhật thông tin Phòng/Đội
    public function update(Request $request, $id)
    {
        // Kiểm tra thông tin đầu vào
        $validated = $request->validate([
            // 'ma_phong' => 'required|unique:App\Models\Phong,ma_phong',
            'ten_phong' => 'required',
        ]);

        $phong = Phong::where('ma_phong', $id)->first();
        $phong->ten_phong = $request->ten_phong;
        $phong->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
        $phong->save();

        return redirect()->route('phong.edit', ['id' => $phong->ma_phong])->with('message', 'Đã cập nhật Phòng/Đội thành công');
    }


    // Khóa Phòng/Đội
    public function destroy($id)
    {
        $phong = Phong::where('ma_phong', $id)->first();
        $phong->ma_trang_thai = 0;
        $phong->save();

        return back()->with('message', 'Đã khóa Phòng/Đội');
    }


    // Mở khóa Phòng/Đội
    public function restore($id)
    {
        $phong = Phong::where('ma_phong', $id)->first();
        $phong->ma_trang_thai = 1;
        $phong->save();

        return back()->with('message', 'Đã mở khóa Phòng/Đội');
    }


    // Lấy danh sách Phòng/Đội dựa trên Đơn vị
    public function dmPhong(Request $request)
    {
        $data['phong'] = Phong::where('ma_don_vi_cap_tren', $request->ma_don_vi)
            ->get(['ma_phong', 'ten_phong']);

        return response()->json($data);
    }
}
