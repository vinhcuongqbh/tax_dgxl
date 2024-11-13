<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use Illuminate\Http\Request;

class DonViController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:xem danh sách đơn vị', ['only' => ['index']]);
        $this->middleware('permission:tạo đơn vị', ['only' => ['create', 'store']]);
        $this->middleware('permission:cập nhật đơn vị', ['only' => ['update', 'edit']]);
        $this->middleware('permission:xem đơn vị', ['only' => ['show']]);
        $this->middleware('permission:khóa đơn vị', ['only' => ['destroy']]);
        $this->middleware('permission:mở khóa đơn vị', ['only' => ['restore']]);
    }

    // Hiển thị danh sách Đơn vị
    public function index()
    {
        $don_vi = DonVi::leftjoin('don_vi as dv2', 'dv2.ma_don_vi', 'don_vi.ma_don_vi_cap_tren')
            ->select('don_vi.id', 'don_vi.ma_don_vi', 'don_vi.ten_don_vi', 'dv2.ten_don_vi as ten_don_vi_cap_tren', 'don_vi.ma_trang_thai')
            ->get();

        return view('donvi.index', ['don_vi' => $don_vi]);
    }


    // Tạo mới Đơn vị
    public function create()
    {
        $don_vi = DonVi::where('ma_trang_thai', 1)->get();

        return view('donvi.create', ['don_vi' => $don_vi]);
    }


    // Lưu trữ thông tin Đơn vị
    public function store(Request $request)
    {
        // Kiểm tra thông tin đầu vào
        $validated = $request->validate([
            'ma_don_vi' => 'required|unique:App\Models\DonVi,ma_don_vi',
            'ten_don_vi' => 'required',
        ]);

        $don_vi = new DonVi();
        $don_vi->ma_don_vi = $request->ma_don_vi;
        $don_vi->ten_don_vi = $request->ten_don_vi;
        $don_vi->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
        $don_vi->ma_trang_thai = 1;
        $don_vi->save();

        return redirect()->route('donvi.edit', ['id' => $request->ma_don_vi])->with('message', 'Đã tạo mới Đơn vị thành công');
    }


    // Sửa thông tin Đơn vị
    public function edit($id)
    {
        $don_vi = DonVi::where('ma_don_vi', $id)->first();
        $dm_don_vi = DonVi::all();


        return view('donvi.edit', [
            'don_vi' => $don_vi,
            'dm_don_vi' => $dm_don_vi
        ]);
    }


    // Cập nhật thông tin Đơn vị
    public function update(Request $request, $id)
    {
        // Kiểm tra thông tin đầu vào
        $validated = $request->validate([
            // 'ma_don_vi' => 'required|unique:App\Models\DonVi,ma_don_vi',
            'ten_don_vi' => 'required',
        ]);

        $don_vi = DonVi::where('ma_don_vi', $id)->first();
        $don_vi->ten_don_vi = $request->ten_don_vi;
        $don_vi->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
        $don_vi->save();
        return redirect()->route('donvi.edit', ['id' => $don_vi->ma_don_vi])->with('message', 'Đã cập nhật Đơn vị thành công');
    }

    // Khóa Đơn vị
    public function destroy($id)
    {
        $don_vi = DonVi::where('ma_don_vi', $id)->first();
        $don_vi->ma_trang_thai = 0;
        $don_vi->save();

        return back()->with('message', 'Đã khóa Đơn vị');
    }


    // Mở khóa Đơn vị
    public function restore($id)
    {
        $don_vi = DonVi::where('ma_don_vi', $id)->first();
        $don_vi->ma_trang_thai = 1;
        $don_vi->save();

        return back()->with('message', 'Đã mở khóa Đơn vị');
    }
}
